<?php

declare(strict_types=1);

namespace Owl\Component\Core\Exception;

final class InvalidDocumentConfigurationException extends \RuntimeException
{
    public function __construct(string $message, ?\Exception $previousException = null)
    {
        parent::__construct($message, 0, $previousException);
    }
}
