<?php

declare(strict_types=1);

namespace Owl\Bundle\UiBundle\Twig\Test;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestFormAttributeExtension extends AbstractExtension
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
                'owl_test_form_attribute',
                $this->getTestFormAttribute(...),
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'owl_test_form_attributes',
                function (array $attributes): array {
                    if (!str_starts_with($this->environment, 'test') && $this->isDebugEnabled === false) {
                        return [];
                    }

                    $result = [];

                    foreach ($attributes as $attribute) {
                        $result['data-testid'] = (string) $attribute;
                    }

                    return ['attr' => $result];
                },
                ['is_safe' => ['html']],
            ),
        ];
    }

    /**
     * @return array{attr: non-empty-array<non-falsy-string, string>}|array{}
     */
    public function getTestFormAttribute(string $name): array
    {
        if (str_starts_with($this->environment, 'test') || $this->isDebugEnabled) {
            return ['attr' => ['data-testid' => (string) $name]];
        }

        return [];
    }
}
