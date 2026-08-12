<?php

namespace Ludens\Controller;

use Ludens\Attributes\Get;
use Ludens\Attributes\Post;

final class Home
{
    #[Post('/')]
    public function index(): void
    {
        echo "Home Page\n";
    }

    #[Get('/user/{username}')]
    public function user(string $username): void
    {
        echo "Welcome {$username}\n";
    }
}
