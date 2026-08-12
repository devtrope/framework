<?php

namespace Ludens\Controller;

use Ludens\Get;

final class Home
{
    #[Get(url: '/')]
    public function index(): void
    {
        echo "Home Page\n";
    }

    #[Get(url: '/user/{username}')]
    public function user(string $username): void
    {
        echo "Welcome {$username}\n";
    }
}
