<?php

namespace Ludens\Controller;

use Ludens\Get;
use Ludens\Post;

final class Home
{
    #[Post(url: '/')]
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
