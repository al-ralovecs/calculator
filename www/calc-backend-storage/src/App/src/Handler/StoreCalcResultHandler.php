<?php declare(strict_types=1);

namespace App\Handler;

use App\Entity\CalcResultEntity;
use App\Helper\TokenIssuingHelper;
use App\InputFilter\AuthorizationInputFilter;
use App\InputFilter\InputFilterMessagesAwareTrait;
use App\InputFilter\StoreCalcResultInputFilter;
use App\Service\PreserveNumberOfResultsService;
use Exception;
use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use UnexpectedValueException;

class StoreCalcResultHandler  implements RequestHandlerInterface
{
    use InputFilterMessagesAwareTrait;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PreserveNumberOfResultsService
     */
    private $preserveNumberOfResultsService;

    public function __construct(
        PreserveNumberOfResultsService $preserveNumberOfResultsService,
        LoggerInterface $logger
    ) {
        $this->preserveNumberOfResultsService = $preserveNumberOfResultsService;
        $this->logger = $logger;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info('A new request received', $request->getParsedBody());

        switch ($request->getMethod()) {
            case RequestMethodInterface::METHOD_POST:
                return $this->handlePost($request);
            default:
                throw new UnexpectedValueException(sprintf(
                    'Request method "%s" not supported',
                    $request->getMethod()
                ));
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    private function handlePost(ServerRequestInterface $request): ResponseInterface
    {
        $headerValidator = new AuthorizationInputFilter();
        $headerValidator->setData([
            AuthorizationInputFilter::KEY_AUTHORIZATION =>
                $request->getHeaderLine(AuthorizationInputFilter::KEY_AUTHORIZATION)
        ]);

        $bodyValidator = new StoreCalcResultInputFilter();
        $bodyValidator->setData($request->getParsedBody());

        if (! $bodyValidator->isValid()) {
            $this->logger->error(
                'Data provided in request is invalid',
                [ 'errors' => $this->getMessages($bodyValidator) ],
            );

            return new JsonResponse([
                'code' => 402,
                'details'=> 'Your provided data is invalid',
                'body' => $request->getParsedBody(),
                'error' => [ $this->getMessages($bodyValidator) ],
            ], 402);
        }

        $calcResult = new CalcResultEntity();
        $calcResult->setValue($bodyValidator->getValue(StoreCalcResultInputFilter::KEY_CALC_RESULT));

        if ($headerValidator->isValid()) {
            $calcResult->setToken($headerValidator->getValue(AuthorizationInputFilter::KEY_AUTHORIZATION));
        } else {
            $calcResult->setToken((new TokenIssuingHelper())->get());
        }

        try {
            $inMemory = $this->preserveNumberOfResultsService->persist($calcResult);
        } catch (Throwable $t) {
            $this->logger->error(
                'Failed to preserve calc result to database',
                [ $t->getCode() => $t->getMessage() ]
            );

            return new JsonResponse([
                'code' => 500,
                'type' => 'Internal Server error',
                'details' => $t->getMessage()
            ], 500);
        }

        return new JsonResponse([
            'token' => $calcResult->getToken(),
            'in_memory' => $inMemory,
        ], 200);
    }
}
