<?php

namespace App\Exception;

use App\httpCode\ResponseCode;
use Exception;

class UpdateProductsSuccess extends Exception
{
    public function __construct(string $message = "")
    {
        $this->code = ResponseCode::OK;
        $this->message = $message;
    }
}