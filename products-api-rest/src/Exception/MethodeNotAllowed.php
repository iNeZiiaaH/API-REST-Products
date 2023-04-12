<?php

namespace App\Exception;

use App\httpCode\ResponseCode;
use Exception;

class MethodNotAllowed extends Exception
{
    public function __construct(string $message = "")
    {
        $this->code = ResponseCode::METHOD_NOT_ALLOWED;
        $this->message = $message;
    }
}