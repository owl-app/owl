<?php

declare(strict_types=1);

namespace Owl\Component\Core\Filesystem\Exception;

final class FileNotFoundException extends \RuntimeException
{
    public function __construct(string $fileLocation, ?\Exception $previousException = null)
    {
        parent::__construct(sprintf('File "%s" could not be found.', $fileLocation), 0, $previousException);
    }
}
