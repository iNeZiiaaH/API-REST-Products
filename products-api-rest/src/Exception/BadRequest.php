<?php

namespace App\Exception;

use App\httpCode\ResponseCode;
use Exception;

class BadRequest extends Exception
{
    public function __construct(string $message = "")
    {
        $this->code = ResponseCode::BAD_REQUEST;
        $this->message = $message;
    }
}