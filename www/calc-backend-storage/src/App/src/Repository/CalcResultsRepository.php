<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\CalcResultEntity;
use App\Enum\TokenPropsInterface;
use Generator;
use Laminas\Db\Adapter\Adapter;
use RuntimeException;
use UnexpectedValueException;

class CalcResultsRepository
{
    /**
     * @var Adapter
     */
    private $database;

    public function __construct(Adapter $database)
    {
        $this->database = $database;
    }

    public function persist(CalcResultEntity $calcResult): void
    {
        if (null !== $calcResult->getId()) {
            throw new UnexpectedValueException('Cannot add entity with id; update instead');
        }

        if (TokenPropsInterface::TOKEN_EXACT_LENGTH < strlen($calcResult->getToken())) {
            throw new UnexpectedValueException('Provided token length exceeds defined range');
        }

        $sql = <<<SQL
INSERT INTO `calculation_results`
    (`token`, `value`, `created_at`)
    VALUES
    (:token, :value, UNIX_TIMESTAMP())
SQL;

        $params = [
            'token' => $calcResult->getToken(),
            'value' => $calcResult->getValue(),
        ];

        $id = $this->database
            ->query($sql)
            ->execute($params)
            ->getAffectedRows();

        $calcResult->setId((int) $id);
    }

    public function countCalcResults(CalcResultEntity $calcResult): int
    {
        if (null === $calcResult->getToken()) {
            throw new UnexpectedValueException('Cannot count entities without token data');
        }

        $sql = <<<SQL
SELECT COUNT(`id`) FROM `calculation_results` WHERE `token` = :token 
SQL;

        $params = [
            'token' => $calcResult->getToken(),
        ];

        $result = $this->database
            ->query($sql)
            ->execute($params);

        if (0 === $result->count()) {
            throw new RuntimeException('Database provided none result on counting calc results');
        }

        if (1 !== $result->count()) {
            throw new RuntimeException(sprintf('Found %s calc results on count a token "%s" query',
                $result->count(),
                $calcResult->getToken()
            ));
        }

        return (int) $result->current()['COUNT(`id`)'];
    }

    public function deleteFirstRows(CalcResultEntity $calcResult, int $toDelete)
    {
        if (null === $calcResult->getToken()) {
            throw new UnexpectedValueException('Cannot get earliest entity without token data');
        }

        $sql = <<<SQL
DELETE FROM `calculation_results` WHERE `token` = :token ORDER BY `created_at` ASC LIMIT :number
SQL;

        $params = [
            'token' => $calcResult->getToken(),
            'number' => $toDelete,
        ];

        $this->database
            ->query($sql)
            ->execute($params);
    }

    public function findAll(CalcResultEntity $calcResult): Generator
    {
        if (null === $calcResult->getToken()) {
            throw new UnexpectedValueException('Cannot find calc results without token data');
        }

        $sql = <<<SQL
SELECT `id`, `token`, `value`, `created_at` FROM `calculation_results`
    WHERE `token` = :token
SQL;

        $params = [
            'token' => $calcResult->getToken(),
        ];

        $result = $this->database
            ->query($sql)
            ->execute($params);

        foreach ($result as $item) {
            yield $this->hydrate($item);
        }
    }

    private function hydrate(array $data): CalcResultEntity
    {
        $entity = new CalcResultEntity();
        $entity->exchangeArray($data);

        return $entity;
    }
}
