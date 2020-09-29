<?php declare(strict_types=1);

namespace App\Handler;

use App\Entity\CalcResultEntity;
use App\InputFilter\InputFilterMessagesAwareTrait;
use App\InputFilter\AuthorizationInputFilter;
use App\InputFilter\ObtainCalcResultInputFilter;
use App\Repository\CalcResultsRepository;
use App\Service\CalcResultExtractingService;
use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use UnexpectedValueException;

class ObtainCalcResultHandler implements RequestHandlerInterface
{
    use InputFilterMessagesAwareTrait;

    /**
     * @var CalcResultExtractingService
     */
    private $calcResultExtractingService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CalcResultExtractingService $calcResultExtractingService,
        LoggerInterface $logger
    ) {
        $this->calcResultExtractingService = $calcResultExtractingService;
        $this->logger = $logger;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info('A new request received', $request->getHeaders());

        switch ($request->getMethod()) {
            case RequestMethodInterface::METHOD_GET:
                return $this->handleGet($request);
            default:
                throw new UnexpectedValueException(sprintf(
                    'Request method "%s" not supported',
                    $request->getMethod()
                ));
        }
    }

    private function handleGet(ServerRequestInterface $request): ResponseInterface
    {
        $headerValidator = new AuthorizationInputFilter();
        $headerValidator->setData([
            AuthorizationInputFilter::KEY_AUTHORIZATION =>
                $request->getHeaderLine(AuthorizationInputFilter::KEY_AUTHORIZATION)
        ]);

        if (! $headerValidator->isValid()) {
            return new JsonResponse([
                'code' => 403,
                'details' => 'Unauthorized access denied',
                'errors' => [ $this->getMessages($headerValidator) ],
            ], 403);
        }

        $calcResult = new CalcResultEntity();
        $calcResult->setToken($headerValidator->getValue(AuthorizationInputFilter::KEY_AUTHORIZATION));

        $paramValidator = new ObtainCalcResultInputFilter();
        $paramValidator->setData([
            ObtainCalcResultInputFilter::KEY_CALC_RESULT_BLOCK =>
                $request->getAttribute(ObtainCalcResultInputFilter::KEY_CALC_RESULT_BLOCK)
        ]);

        if (! $paramValidator->isValid()) {
            $this->logger->error(
                'Data provided in request is invalid',
                [ 'errors' => $this->getMessages($paramValidator) ],
            );

            return new JsonResponse([
                'code' => 402,
                'details' => 'Your request is invalid',
                'errors' => [ $this->getMessages($paramValidator) ],
            ], 402);
        }

        try {
            $calcResult = $this->calcResultExtractingService->extract(
                $calcResult,
                $paramValidator->getValue(ObtainCalcResultInputFilter::KEY_CALC_RESULT_BLOCK)
            );
        } catch (Throwable $t) {
            $this->logger->error(
                'Failed to extract calc result by its place in memory',
                [ $t->getCode() => $t->getMessage() ]
            );

            return new JsonResponse([
                'code' => 500,
                'details' => 'Failed to extract calc result by its place in memory',
                'error' => $t->getMessage(),
            ], 500);
        }

        return new JsonResponse([
            'calc_result' => $calcResult->getValue(),
        ], 200);
    }
}
