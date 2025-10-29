<?php

namespace Ludens\Http\Responses;

use Ludens\Http\Response;
use Ludens\Core\Application;

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
     * @throws \Exception If the view file does not exist.
     */
    public function __construct(string $viewName, array $data = [])
    {
        parent::__construct();

        $templatesPath = rtrim(Application::getInstance()->path('templates'));

        if (! is_dir($templatesPath)) {
            throw new \Exception("$templatesPath does not exist");
        }

        $filePath = rtrim(Application::getInstance()->path('templates'), '/') . '/' . $viewName . '.php';

        if (! file_exists($filePath)) {
            throw new \Exception("View file $viewName does not exist");
        }

        ob_start();
        extract($data, EXTR_SKIP);
        include $filePath;
        $content = ob_get_clean();

        if (! $content) {
            $content = '';
        }

        $this
            ->setBody($content)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}
