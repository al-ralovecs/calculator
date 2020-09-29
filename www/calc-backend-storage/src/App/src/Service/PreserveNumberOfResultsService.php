<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\CalcResultEntity;
use App\Repository\CalcResultsRepository;
use Psr\Log\LoggerInterface;

class PreserveNumberOfResultsService
{
    public const RESULTS_NUMBER_TO_PRESERVE = 5;

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
     * @param CalcResultEntity $calcResult
     * @return int
     */
    public function persist(CalcResultEntity $calcResult): int
    {
        $this->calcResultsRepository->persist($calcResult);
        $countOfResults = $this->calcResultsRepository->countCalcResults($calcResult);

        $this->logger->info(sprintf('Found %s rows with token "%s"',
            $countOfResults,
            $calcResult->getToken()
        ));

        if (self::RESULTS_NUMBER_TO_PRESERVE < $countOfResults) {
            $this->calcResultsRepository->deleteFirstRows(
                $calcResult,
                $countOfResults - self::RESULTS_NUMBER_TO_PRESERVE
            );
        }

        $countOfResults = $this->calcResultsRepository->countCalcResults($calcResult);

        return $countOfResults;
    }
}
