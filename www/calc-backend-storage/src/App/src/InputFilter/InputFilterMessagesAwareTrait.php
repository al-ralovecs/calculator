<?php declare(strict_types=1);

namespace App\InputFilter;

use Laminas\InputFilter\InputFilterInterface;

trait InputFilterMessagesAwareTrait
{
    protected function getMessages(InputFilterInterface $inputFilter): array
    {
        $messages = [];
        foreach ($inputFilter->getInvalidInput() as $error) {
            $messages[$error->getName()] = $error->getMessages();
        }

        return $messages;
    }
}
