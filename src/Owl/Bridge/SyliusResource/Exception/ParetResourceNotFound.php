<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Exception;

use Exception;

class ParetResourceNotFound extends Exception implements ExceptionInterface
{
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
