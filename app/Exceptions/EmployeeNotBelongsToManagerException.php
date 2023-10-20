<?php

namespace App\Exceptions;

use Exception;

class EmployeeNotBelongsToManagerException extends Exception
{
    public function __construct($message = "Employee does not belong to this manager.", $code = 403)
    {
        parent::__construct($message, $code);
    }
}
