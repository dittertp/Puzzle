<?php

namespace Puzzle\Exceptions;

class ConfigurationException extends \Exception
{
    public function __construct($message = 'Not Found') {
        parent::__construct($message, 404);
    }
}
