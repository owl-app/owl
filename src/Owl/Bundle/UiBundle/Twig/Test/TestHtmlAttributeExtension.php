<?php

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\Twig\Test;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestHtmlAttributeExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $environment,
        private readonly bool $isDebugEnabled,
    ) {
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'owl_test_html_attribute',
                function (string $name): string {
                    if (str_starts_with($this->environment, 'test') || $this->isDebugEnabled) {
                        return sprintf('data-testid="%s"', (string) $name);
                    }

                    return '';
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
