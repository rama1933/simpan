<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;
use Inertia\Inertia;
use Inertia\Response;

abstract class BaseController extends LaravelController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Render Inertia page with success message
     */
    protected function renderPage(string $component, array $props = [], string $message = null): Response
    {
        if ($message) {
            session()->flash('success', $message);
        }

        return Inertia::render($component, $props);
    }

    /**
     * Render Inertia page with error message
     */
    protected function renderPageWithError(string $component, array $props = [], string $message = null): Response
    {
        if ($message) {
            session()->flash('error', $message);
        }

        return Inertia::render($component, $props);
    }

    /**
     * Redirect with success message
     */
    protected function redirectWithSuccess(string $route, string $message): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Redirect with error message
     */
    protected function redirectWithError(string $route, string $message): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($route)->with('error', $message);
    }

    /**
     * Redirect back with success message
     */
    protected function backWithSuccess(string $message): \Illuminate\Http\RedirectResponse
    {
        return back()->with('success', $message);
    }

    /**
     * Redirect back with error message
     */
    protected function backWithError(string $message): \Illuminate\Http\RedirectResponse
    {
        return back()->with('error', $message);
    }

    /**
     * Return JSON success response
     */
    protected function jsonSuccess($data = null, string $message = 'Success', int $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return JSON error response
     */
    protected function jsonError(string $message = 'Error occurred', int $code = 400, $data = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return JSON validation error response
     */
    protected function jsonValidationError($errors, string $message = 'Validation failed'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }
}
