<?php

namespace App\Exception;

use Exception;

class ImportException extends Exception
{
    /**
     * @return ImportException
     */
    public static function create(): self
    {
        return new self("Invalid import data");
    }
}