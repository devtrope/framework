<?php

namespace Ludens\Http\Responses;

use Ludens\Http\Response;

/**
 * Handles HTTP redirection responses.
 * 
 * @package Ludens\Http\Responses
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class RedirectResponse extends Response
{
    /**
     * @param string|null $url
     * @param int $statusCode
     *
     * @throws \Exception If no URL is provided.
     */
    public function __construct(?string $url, int $statusCode)
    {
        parent::__construct();

        if ($url === null) {
            throw new \Exception('URL for redirection cannot be null');
        }

        $this
            ->setHeader('Location', $url)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody('')
            ->setCode($statusCode);
    }
}
