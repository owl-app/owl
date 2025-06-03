<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\NotificationFile;
use Owl\Component\File\Model\File;
use PHPUnit\Framework\TestCase;

final class NotificationFileTest extends TestCase
{
    public function testIsInstanceOfFile(): void
    {
        $notificationFile = new NotificationFile();
        self::assertInstanceOf(File::class, $notificationFile);
    }
} 