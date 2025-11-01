<?php

namespace Ludens\Http\Responses;

use Ludens\Http\Response;

/**
 * Handles JSON responses.
 * 
 * @package Ludens\Http\Responses
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class JsonResponse extends Response
{
    /**
     * @param array $data
     *
     * @throws \Exception If JSON encoding fails.
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $jsonData = json_encode($data);
        if (! $jsonData) {
            throw new \Exception('Failed to encode data to JSON');
        }

        $this
            ->setBody($jsonData)
            ->setHeader('Content-Type', 'application/json; charset=UTF-8');
    }
}
