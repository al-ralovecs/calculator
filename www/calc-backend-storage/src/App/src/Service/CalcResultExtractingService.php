<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\CalcResultEntity;
use App\Repository\CalcResultsRepository;
use Psr\Log\LoggerInterface;
use RuntimeException;

class CalcResultExtractingService
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
     * @param CalcResultEntity $calcResult
     * @param int $number
     * @return CalcResultEntity
     */
    public function extract(CalcResultEntity $calcResult, int $number): CalcResultEntity
    {
        $currentAmountOfResults = $this->calcResultsRepository->countCalcResults($calcResult);

        $this->logger->info(sprintf('Found %s rows with token "%s"',
            $currentAmountOfResults,
            $calcResult->getToken()
        ));

        if ($number + 1 > $currentAmountOfResults) {
            throw new \UnexpectedValueException(sprintf(
                'There does not exist calc result with token "%s" and number %s',
                $calcResult->getToken(),
                $number
            ));
        }

        $step = 0;
        foreach ($this->calcResultsRepository->findAll($calcResult) as $result) {
            if ($step !== $number) {
                $step++;

                continue;
            }

            $calcResult = $result;
            break;
        }

        if (null === $calcResult->getValue()) {
            throw new RuntimeException('Failed to obtain calc result');
        }

        return $calcResult;
    }
}
