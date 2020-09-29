<?php declare(strict_types=1);

namespace App\Helper;

use App\Enum\TokenPropsInterface;
use Exception;

class TokenIssuingHelper
{
    /**
     * @return string
     * @throws Exception
     */
    public function get(): string
    {
        return bin2hex(random_bytes(TokenPropsInterface::TOKEN_EXACT_LENGTH / 2));
    }
}
