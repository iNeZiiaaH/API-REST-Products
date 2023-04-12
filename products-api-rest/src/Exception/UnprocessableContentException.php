<?php

namespace App\Crud\Exception;

use App\httpCode\ResponseCode;
use Exception;

class UnprocessableContentException extends Exception
{
    public function __construct (string $message = "")
    {
        $this->code = responseCode::UNPROCESSABLE_CONTENT;
        $this->message = $message;
    }
}
