<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Handle success response
     */
    protected function success($data = null, $message = 'Success', $code = 200)
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code
        ];
    }

    /**
     * Handle error response
     */
    protected function error($message = 'Error occurred', $code = 400, $data = null)
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code
        ];
    }

    /**
     * Handle validation error response
     */
    protected function validationError($errors, $message = 'Validation failed')
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'code' => 422
        ];
    }
}

