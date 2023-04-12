<?php

namespace App\Exception;

use Exception;

class ExceptionsHandler
{
    static function sendError(Exception $e): void
    {
        http_response_code($e->getCode());
        echo json_encode([
            'error' => 'An error occurred',
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ]);
    }
}