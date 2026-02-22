<?php

namespace RianWlp\Libs\exceptions;

class NotFoundException extends \Exception
{
    public function __construct($message = 'Registro não encontrado', $code = 404, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
