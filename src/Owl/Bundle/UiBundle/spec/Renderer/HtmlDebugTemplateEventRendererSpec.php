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
use Owl\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class HtmlDebugTemplateEventRendererSpec extends ObjectBehavior
{
    public function let(TemplateEventRendererInterface $templateEventRenderer, TemplateBlockRegistryInterface $templateBlockRegistry): void
    {
        $this->beConstructedWith($templateEventRenderer, $templateBlockRegistry);
    }

    public function it_is_a_template_event_renderer(): void
    {
        $this->shouldImplement(TemplateEventRendererInterface::class);
    }

    public function it_renders_html_debug_comment_if_at_least_one_template_is_a_html_one(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry,
    ): void {
        $firstTemplateBlock = new TemplateBlock('first_block', 'event_block', 'firstBlock.txt.twig', [], 0, true);
        $secondTemplateBlock = new TemplateBlock('second_block', 'event_block', 'secondBlock.html.twig', [], 0, true);

        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([$firstTemplateBlock, $secondTemplateBlock]);

        $templateEventRenderer->render(['best_event_ever'], ['foo' => 'bar'])->willReturn("First block\nSecond block");

        $this->render(['best_event_ever'], ['foo' => 'bar'])->shouldReturn(
            '<!-- BEGIN EVENT | event name: "best_event_ever" -->' . "\n" .
            'First block' . "\n" .
            'Second block' . "\n" .
            '<!-- END EVENT | event name: "best_event_ever" -->',
        );
    }

    public function it_does_not_render_html_debug_comment_if_no_html_templates_are_found(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry,
    ): void {
        $firstTemplateBlock = new TemplateBlock('first_block', 'event_block', 'firstBlock.txt.twig', [], 0, true);
        $secondTemplateBlock = new TemplateBlock('second_block', 'event_block', 'secondBlock.txt.twig', [], 0, true);

        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([$firstTemplateBlock, $secondTemplateBlock]);

        $templateEventRenderer->render(['best_event_ever'], ['foo' => 'bar'])->willReturn("First block\nSecond block");

        $this->render(['best_event_ever'], ['foo' => 'bar'])->shouldReturn("First block\nSecond block");
    }

    public function it_returns_html_debug_comment_if_no_blocks_are_found_for_an_event(
        TemplateEventRendererInterface $templateEventRenderer,
        TemplateBlockRegistryInterface $templateBlockRegistry,
    ): void {
        $templateBlockRegistry->findEnabledForEvents(['best_event_ever'])->willReturn([]);

        $templateEventRenderer->render(Argument::cetera())->willReturn('');

        $this->render(['best_event_ever'])->shouldReturn(
            '<!-- BEGIN EVENT | event name: "best_event_ever" -->' . "\n" .
            "\n" .
            '<!-- END EVENT | event name: "best_event_ever" -->',
        );
    }
}
