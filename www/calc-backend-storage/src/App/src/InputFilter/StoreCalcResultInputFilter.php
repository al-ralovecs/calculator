<?php declare(strict_types=1);

namespace App\InputFilter;

use Laminas\Filter;
use Laminas\InputFilter\{Input, InputFilter};
use Laminas\Validator;

class StoreCalcResultInputFilter extends InputFilter
{
    public const
        KEY_CALC_RESULT = 'calc_result';

    public function __construct()
    {
        $this->addInputCalcResult();
    }

    private function addInputCalcResult(): void
    {
        $calcResult = new Input(self::KEY_CALC_RESULT);

        $calcResult
            ->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\ToFloat());

        $calcResult
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\Callback(
                function($value) {
                    return is_numeric($value);
                }
            ));

        $this->add($calcResult);
    }
}
