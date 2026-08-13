<?php

namespace Ludens\Controller;

use Ludens\Routing\Routes;

final class Home
{
    #[Routes\Post('/')]
    public function index(): void
    {
        echo "Home Page\n";
    }

    #[Routes\Get('/user/{username}')]
    public function user(string $username): void
    {
        echo "Welcome {$username}\n";
    }
}
