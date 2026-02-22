<?php

namespace RianWlp\Libs\exceptions;

class ValidationException extends \Exception
{
    // Validation error
    public function __construct($message = 'Erro na validação', $code = 422, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
