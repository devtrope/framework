<?php

namespace Ludens\Controller;

use Ludens\Routing\Routes;

final class Home
{
    #[Routes\Post('/')]
    public function index(): string
    {
        return "Home Page\n";
    }

    #[Routes\Get('/user/{username}')]
    public function user(string $username): string
    {
        return "Welcome {$username}\n";
    }
}
