<?php declare(strict_types=1);

namespace App\InputFilter;

use Laminas\Filter;
use Laminas\InputFilter\{Input, InputFilter};
use Laminas\Validator;

class ObtainCalcResultInputFilter extends InputFilter
{
    public const
        KEY_CALC_RESULT_BLOCK = 'block';

    public function __construct()
    {
        $this->addInputBlock();
    }

    private function addInputBlock(): void
    {
        $block = new Input(self::KEY_CALC_RESULT_BLOCK);

        $block
            ->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\ToInt());

        $block
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\LessThan([ 'max' => 4, 'inclusive' => true ]))
            ->attach(new Validator\GreaterThan( ['min' => 0, 'inclusive' => true ]));

        $this->add($block);
    }
}
