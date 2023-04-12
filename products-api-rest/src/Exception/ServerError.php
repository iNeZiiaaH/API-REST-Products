<?php

namespace App\Exception;

use App\httpCode\ResponseCode;
use Exception;

class InternalServerError extends Exception
{
    public function __construct(string $message = "")
    {
        $this->code = ResponseCode::INTERNAL_SERVER_ERROR;
        $this->message = $message;
    }
}