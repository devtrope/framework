<?php

namespace Ludens\Framework\Controller;

use Ludens\Http\Response;
use Ludens\Http\Validation\Validator;
use Ludens\Http\Responses\JsonResponse;
use Ludens\Http\Responses\ViewResponse;
use Ludens\Http\Responses\RedirectResponse;

/**
 * Base controller class providing common functionality.
 *
 * All application controllers should extend this class.
 *
 * @package Ludens\Http\Controller
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
abstract class AbstractController
{
    /**
     * Render a view template.
     *
     * @param string $viewName View name (e.g., 'users/index')
     * @param array $data Data to pass to the view
     * @return ViewResponse
     *
     * @example
     * return $this->render('home', ['title' => 'Welcome']);
     */
    public function render(string $viewName, array $data = []): ViewResponse
    {
        return Response::view($viewName, $data);
    }

    /**
     * Create a JSON response.
     *
     * @param array $data Data to encode as JSON
     * @return JsonResponse
     *
     * @example
     * return $this->json(['user' => $user]);
     */
    public function json(array $data): JsonResponse
    {
        return Response::json($data);
    }

    /**
     * Redirect to a URL.
     *
     * @param string|null $url
     * @param int $statusCode
     * @return RedirectResponse
     *
     * @example
     * return $this->redirect('/users');
     */
    public function redirect(?string $url, int $statusCode = 302): RedirectResponse
    {
        return Response::redirect($url, $statusCode);
    }

    /**
     * Redirect to the referer.
     *
     * @param int $statusCode
     * @return RedirectResponse
     *
     * @example
     * return $this->back();
     */
    public function back(int $statusCode = 302): RedirectResponse
    {
        return Response::back($statusCode);
    }

    /**
     * Create a new Validator.
     *
     * @return Validator
     *
     * @example
     * $data = $this->validator()->fields($request, [
     *     'email' => $this->validator()->rule()->required()->email(),
     *     'password' => $this->validator()->rule()->required()->minLength(8)
     * ]);
     */
    public function validator(): Validator
    {
        return new Validator();
    }
}
