<?php

namespace Ludens\Http;

/**
 * Enum representing HTTP methods.
 *
 * @package Ludens\Http
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
enum HttpMethod: string
{
    case GET    = 'GET';
    case POST   = 'POST';
    case PUT    = 'PUT';
    case PATCH  = 'PATCH';
    case DELETE = 'DELETE';
}
