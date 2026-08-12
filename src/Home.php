<?php

namespace Ludens;

final class Home
{
    public function index(): void
    {
        echo "Home Page\n";
    }

    public function user(string $username): void
    {
        echo "Welcome {$username}\n";
    }
}
