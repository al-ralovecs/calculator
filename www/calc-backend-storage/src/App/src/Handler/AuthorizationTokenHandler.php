<?php declare(strict_types=1);

namespace App\Handler;

use App\Entity\CalcResultEntity;
use App\Helper\TokenIssuingHelper;
use App\InputFilter\InputFilterMessagesAwareTrait;
use App\InputFilter\AuthorizationInputFilter;
use App\Repository\CalcResultsRepository;
use App\Service\PreserveNumberOfResultsService;
use Exception;
use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class AuthorizationTokenHandler implements RequestHandlerInterface
{

    /**
     * @var CalcResultsRepository
     */
    private $calcResultsRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CalcResultsRepository $calcResultsRepository,
        LoggerInterface $logger
    ) {
        $this->calcResultsRepository = $calcResultsRepository;
        $this->logger = $logger;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
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

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    private function handleGet(ServerRequestInterface $request): ResponseInterface
    {
        if (! isset($request->getHeaderLine(AuthorizationInputFilter::KEY_AUTHORIZATION)[0])) {
            return new JsonResponse([
                'token' => (new TokenIssuingHelper())->get(),
                'in_memory' => 0,
            ], 200);
        }

        $headerValidator = new AuthorizationInputFilter();
        $headerValidator->setData([
            AuthorizationInputFilter::KEY_AUTHORIZATION =>
                $request->getHeaderLine(AuthorizationInputFilter::KEY_AUTHORIZATION)
        ]);

        if (! $headerValidator->isValid()) {
            return new JsonResponse([
                'token' => (new TokenIssuingHelper())->get(),
                'in_memory' => 0,
            ], 200);
        }

        $calcResult = new CalcResultEntity();
        $calcResult->setToken($headerValidator->getValue(AuthorizationInputFilter::KEY_AUTHORIZATION));
        $inMemory = $this->calcResultsRepository->countCalcResults($calcResult);

        return new JsonResponse([
            'token' => $calcResult->getToken(),
            'in_memory' => $inMemory,
        ], 200);
    }
}
