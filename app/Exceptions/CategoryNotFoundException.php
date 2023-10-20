<?php

namespace App\Exceptions;

use Exception;

class CategoryNotFoundException extends Exception
{

    public function __construct($message = "Category not found", $code = 403)
    {
        parent::__construct($message, $code);
    }
}
