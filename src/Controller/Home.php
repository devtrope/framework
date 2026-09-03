<?php

namespace Ludens\Controller;

use Ludens\Http\Response;
use Ludens\Routing\Routes;

final class Home
{
    #[Routes\Post('/')]
    public function index(): Response
    {
        $response = new Response();
        return $response->setBody("Home Page\n");
    }

    #[Routes\Get('/user/{username}')]
    public function user(string $username): Response
    {
        $response = new Response();
        return $response->setBody("Welcome {$username}\n");
    }
}
