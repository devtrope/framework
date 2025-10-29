<?php

namespace Ludens\Http\Controller;

use Ludens\Http\Response;

abstract class AbstractController
{
    public function render(string $viewName, array $data = []): Response
    {
        return Response::view($viewName, $data);
    }

    public function json(array $data): Response
    {
        return Response::json($data);
    }
}
