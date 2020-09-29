<?php declare(strict_types=1);

namespace App\Tests\unit\Helper;

use App\Helper\TokenIssuingHelper;

class TokenIssuingHelperTest extends \Codeception\Test\Unit
{
    public function testTokenLength(): void
    {
        $token = (new TokenIssuingHelper())->get();

        $this->assertEquals(56, strlen($token));
    }
}
