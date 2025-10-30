<?php

namespace Ludens\Http\Responses;

use Ludens\Http\Response;
use Ludens\Framework\View\ViewRenderer;

/**
 * Handles responses with templates.
 * 
 * @package Ludens\Http\Responses
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ViewResponse extends Response
{
    /**
     * @param string $viewName
     * @param array $data
     *
     * @throws \Exception If the view file does not exist.
     */
    public function __construct(string $viewName, array $data = [])
    {
        parent::__construct();

        if (! str_ends_with($viewName, '.html.twig')) {
            $viewName .= '.html.twig';
        }

        $content = ViewRenderer::render($viewName, $data);

        $this
            ->setBody($content)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}
