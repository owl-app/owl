<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\SuggestionFile;
use Owl\Component\File\Model\File;
use PHPUnit\Framework\TestCase;

final class SuggestionFileTest extends TestCase
{
    public function testIsInstanceOfFile(): void
    {
        $suggestionFile = new SuggestionFile();
        self::assertInstanceOf(File::class, $suggestionFile);
    }
}
