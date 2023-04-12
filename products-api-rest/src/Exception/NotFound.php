<?php

namespace App\Exception;

use App\httpCode\ResponseCode;
use Exception;

class NotFound extends Exception
{
    public function __construct(string $message = "")
    {
        $this->code = ResponseCode::NOT_FOUND;
        $this->message = $message;
    }
}