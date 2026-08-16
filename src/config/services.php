<?php

declare(strict_types=1);

return [
    \Ludens\Routing\RoutesRegisterer::class => [
        'bind' => [
            'controllerFolder' => 'src/Controller/',
            'controllerNamespace' => '\\Ludens\\Controller\\'
        ]
    ],
];