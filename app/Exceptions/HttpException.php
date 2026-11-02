<?php

namespace Educse\Exceptions;

final class HttpException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $status = 422)
    {
        parent::__construct($message);
    }
}
