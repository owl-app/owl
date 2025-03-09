<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @internal
 *
 * @experimental
 */
final class TemplateBlockDataCollector extends DataCollector
{
    /** @var TemplateBlockRenderingHistory */
    private $templateBlockRenderingHistory;

    public function __construct(TemplateBlockRenderingHistory $templateBlockRenderingHistory)
    {
        $this->templateBlockRenderingHistory = $templateBlockRenderingHistory;
        $this->reset();
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data['renderedEvents'] = $this->templateBlockRenderingHistory->getRenderedEvents();
    }

    public function getRenderedEvents(): array
    {
        return $this->data['renderedEvents'];
    }

    /**
     * @return int<0, max>
     */
    public function getNumberOfRenderedEvents(): int
    {
        return count($this->data['renderedEvents']);
    }

    /**
     * @return int<min, max>
     */
    public function getNumberOfRenderedBlocks(): int
    {
        return array_reduce($this->data['renderedEvents'], /**
         * @return int<min, max>
         */
            static function (int $accumulator, array $event): int {
                return $accumulator + count($event['blocks']);
            }, 0);
    }

    public function getTotalDuration(): float
    {
        return array_reduce($this->data['renderedEvents'], static function (float $accumulator, array $event): float {
            return $accumulator + $event['time'];
        }, 0.0);
    }

    /**
     * @return 'sylius_ui.template_block'
     */
    public function getName(): string
    {
        return 'sylius_ui.template_block';
    }

    public function reset(): void
    {
        $this->data['renderedEvents'] = [];
    }
}
