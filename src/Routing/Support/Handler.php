<?php

namespace Ludens\Routing\Support;

use Ludens\Exceptions\InvalidControllerException;
use Ludens\Exceptions\InvalidMethodException;

final class Handler
{
    /**
     * @var string
     */
    private string $controller;

    /**
     * @var string
     */
    private string $method;

    /**
     * @param string $controller
     * @param string $method
     */
    public function __construct(string $controller, string $method)
    {
        $this->validate($controller, $method);
        $this->controller = $controller;
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $controller
     * @param string $method
     * @throws InvalidControllerException
     * @throws InvalidMethodException
     * @return void
     */
    private function validate(string $controller, string $method): void
    {
        if (false === class_exists($controller)) {
            throw new InvalidControllerException(\sprintf(
                'The controller %s does not exist',
                $controller
            ));
        }

        if (false === method_exists($controller, $method)) {
            throw new InvalidMethodException(\sprintf(
                'The method %s does not exist in controller %s',
                $method,
                $controller
            ));
        }
    }
}
