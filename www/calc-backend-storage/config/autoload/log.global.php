<?php declare(strict_types=1);

return [
    \Langue\Log\Logger::class => [
        'writers' => [
            'stream' => [
                'name' => 'noop',
                //'name' => 'stream',
                'options' => [
                    'stream' => __DIR__ . '/../../data/logs/app.log',
                    'formatter' => [
                        'name' => 'simple',
                        'options' => [
                            'format' => '%timestamp% %priorityName% %message% %extra%',
                            'dateTimeFormat' => 'Y-m-d H:i:s',
                        ],
                    ],
                ],
            ],
        ],
        'processors' => [
            'psr_placeholder' => [
                'name' => 'psrplaceholder',
            ],
        ],
        'level' => null,
    ],
];
