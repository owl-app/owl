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

namespace Owl\Bundle\UiBundle\Twig;

use Owl\Bundle\UiBundle\Renderer\TemplateEventRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @experimental
 */
final class TemplateEventExtension extends AbstractExtension
{
    /** @var TemplateEventRendererInterface */
    private $templateEventRenderer;

    public function __construct(TemplateEventRendererInterface $templateEventRenderer)
    {
        $this->templateEventRenderer = $templateEventRenderer;
    }

    /**
     * @return list{TwigFunction}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_template_event', [$this, 'render'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string|string[] $eventName
     */
    public function render($eventName, array $context = []): string
    {
        return $this->templateEventRenderer->render((array) $eventName, $context);
    }
}
