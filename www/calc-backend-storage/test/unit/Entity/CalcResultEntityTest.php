<?php declare(strict_types=1);

namespace App\Tests\unit\Entity;

use App\Entity\CalcResultEntity;
use DateTime;

class CalcResultEntityTest extends \Codeception\Test\Unit
{
    public function testEntityConsistencyOnValueWithoutDecimals(): void
    {
        $entity = new CalcResultEntity();
        $entity->exchangeArray([
            'id' => 1,
            'token' => 'sdkfvmdkfosf9439r3ijfcmwefmcefif4949mfcefiejfjiefjierifm',
            'value' => 21,
            'created_at' => 1600954582,
        ]);

        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('sdkfvmdkfosf9439r3ijfcmwefmcefif4949mfcefiejfjiefjierifm', $entity->getToken());
        $this->assertEquals(21, $entity->getValue());
        $this->assertEquals(1600954582, $entity->getCreatedAt()->getTimestamp());
    }

    public function testEntityConsistencyOnValueWithDecimals(): void
    {
        $entity = new CalcResultEntity();
        $entity->exchangeArray([
            'id' => 1,
            'token' => 'sdkfvmdkfosf9439r3ijfcmwefmcefif4949mfcefiejfjiefjierifm',
            'value' => 21.9909,
            'created_at' => 1600954582,
        ]);

        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('sdkfvmdkfosf9439r3ijfcmwefmcefif4949mfcefiejfjiefjierifm', $entity->getToken());
        $this->assertEquals(21.9909, $entity->getValue());
        $this->assertEquals(1600954582, $entity->getCreatedAt()->getTimestamp());
    }

    public function testEntityConsistencyWithSetters(): void
    {
        $entity = new CalcResultEntity();
        $entity->setId(1);
        $entity->setToken('sdkfvmdkfosf9439r3ijfcmwefmcefif4949mfcefiejfjiefjierifm');
        $entity->setValue(21.9909);

        $dateTime = new DateTime();
        $entity->setCreatedAt($dateTime);

        $this->assertEquals(1, $entity->getId());
        $this->assertEquals('sdkfvmdkfosf9439r3ijfcmwefmcefif4949mfcefiejfjiefjierifm', $entity->getToken());
        $this->assertEquals(21.9909, $entity->getValue());
        $this->assertEquals($dateTime->getTimestamp(), $entity->getCreatedAt()->getTimestamp());
    }
}
