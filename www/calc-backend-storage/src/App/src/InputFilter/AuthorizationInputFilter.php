<?php declare(strict_types=1);

namespace App\InputFilter;

use App\Enum\TokenPropsInterface;
use Laminas\Filter;
use Laminas\InputFilter\{Input, InputFilter};
use Laminas\Validator;

class AuthorizationInputFilter extends InputFilter
{
    public const
        KEY_AUTHORIZATION = 'authorization';

    public function __construct()
    {
        $this->addInputToken();
    }

    private function addInputToken(): void
    {
        $token = new Input(self::KEY_AUTHORIZATION);

        $token
            ->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StringToLower());

        $token
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\StringLength([
                'min' => TokenPropsInterface::TOKEN_EXACT_LENGTH,
                'max' => TokenPropsInterface::TOKEN_EXACT_LENGTH,
            ]));

        $this->add($token);
    }
}
