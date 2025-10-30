<?php

namespace Ludens\Http\Responses;

use Ludens\Http\Response;
use Ludens\Framework\View\ErrorRenderer;

/**
 * Handles error responses with templates.
 * 
 * @package Ludens\Http\Responses
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ErrorResponse extends Response
{
    public static function notFound(?string $message = null): self
    {
        return self::errorPage(404, $message); 
    }

    public static function errorPage(int $statusCode, ?string $message): Response
    {
        $template = "errors/{$statusCode}.html.twig";

        $content = ErrorRenderer::render($template, [
            'message' => $message,
            'code' => $statusCode
        ]);

        $response = new self();
        
        return $response->setBody($content)->setCode($statusCode)->setHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}
