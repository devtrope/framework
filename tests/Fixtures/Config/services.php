<?php

declare(strict_types=1);

return [
    Tests\Fixtures\Services\ServiceWithBoundValue::class => [
        'bind' => [
            'apiKey' => 'config-secret-key'
        ]
    ],
];
