<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\SuggestionStatus;
use Owl\Component\Core\Model\SuggestionStatusInterface;
use PHPUnit\Framework\TestCase;

final class SuggestionStatusTest extends TestCase
{
    private SuggestionStatus $status;

    protected function setUp(): void
    {
        $this->status = new SuggestionStatus();
    }

    public function testImplementsSuggestionStatusInterface(): void
    {
        self::assertInstanceOf(SuggestionStatusInterface::class, $this->status);
    }

    public function testGetStatusesLabels(): void
    {
        $labels = $this->status->getStatusesLabels();
        self::assertArrayHasKey('new', $labels);
        self::assertArrayHasKey('in_progress', $labels);
        self::assertArrayHasKey('realized', $labels);
        self::assertArrayHasKey('cancelled', $labels);
    }
} 