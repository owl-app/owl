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
use Owl\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface;
use PhpSpec\ObjectBehavior;
use Twig\Environment;

final class TwigTemplateBlockRendererSpec extends ObjectBehavior
{
    public function let(Environment $twig): void
    {
        $this->beConstructedWith($twig);
    }

    public function it_is_a_template_block_renderer(): void
    {
        $this->shouldImplement(TemplateBlockRendererInterface::class);
    }

    public function it_renders_a_template_block(Environment $twig): void
    {
        $twig->render('block.txt.twig', ['foo' => 'bar'])->willReturn('Block content');

        $this->render(
            new TemplateBlock('block_name', 'event_name', 'block.txt.twig', [], 0, true),
            ['foo' => 'bar'],
        )->shouldReturn('Block content');
    }

    public function it_merges_template_block_context_with_passed_context(Environment $twig): void
    {
        $twig->render('block.txt.twig', ['sample' => 'Hello', 'switch' => true])->willReturn('Block content');

        $this->render(
            new TemplateBlock('block_name', 'event_name', 'block.txt.twig', ['sample' => 'Hi', 'switch' => true], 0, true),
            ['sample' => 'Hello'],
        )->shouldReturn('Block content');
    }
}
