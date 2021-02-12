<?php


namespace App\Exceptions;
use Exception;

class WoTException extends Exception
{
    private $array;

    public function __construct($message, $array, $code = 0, Exception $previous = null) {
        $this->array = $array;

        parent::__construct($message, $code, $previous);
    }

    public function getArray()
    {
        return $this->array;
    }
}