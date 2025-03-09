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

namespace spec\Owl\Bundle\UiBundle\Renderer;

use Owl\Bundle\UiBundle\Registry\TemplateBlock;
use Owl\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Owl\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;
use Owl\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class DelegatingTemplateEventRendererSpec extends ObjectBehavior
{
    public function let(TemplateBlockRegistryInterface $templateBlockRegistry, TemplateBlockRendererInterface $templateBlockRenderer): void
    {
        $this->beConstructedWith($templateBlockRegistry, $templateBlockRenderer);
    }

    public function it_is_a_template_event_renderer(): void
    {
        $this->shouldImplement(TemplateEventRendererInterface::class);
    }

    public function it_renders_template_events(
        TemplateBlockRegistryInterface $templateBlockRegistry,
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $firstTemplateBlock = new TemplateBlock('first_block', 'best_event_ever', 'firstBlock.txt.twig', [], 0, true);
        $secondTemplateBlock = new TemplateBlock('second_block', 'best_event_ever', 'secondBlock.txt.twig', [], 0, true);

        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([$firstTemplateBlock, $secondTemplateBlock]);

        $templateBlockRenderer->render($firstTemplateBlock, ['foo' => 'bar'])->willReturn('First block');
        $templateBlockRenderer->render($secondTemplateBlock, ['foo' => 'bar'])->willReturn('Second block');

        $this->render(['best_event_ever'], ['foo' => 'bar'])->shouldReturn("First block\nSecond block");
    }

    public function it_returns_an_empty_string_if_no_blocks_are_found_for_an_event(
        TemplateBlockRegistryInterface $templateBlockRegistry,
        TemplateBlockRendererInterface $templateBlockRenderer,
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([]);

        $templateBlockRenderer->render(Argument::cetera())->shouldNotBeCalled();

        $this->render(['best_event_ever'])->shouldReturn('');
    }
}
