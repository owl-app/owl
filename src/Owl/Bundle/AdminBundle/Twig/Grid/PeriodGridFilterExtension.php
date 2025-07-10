<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Grid;

use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Owl\Component\Core\Translation\MonthTranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PeriodGridFilterExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        private MonthTranslatorInterface $monthTranslator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('owl_period_grid_filter_generate_link_navigation', [$this, 'generateLinkNavigation']),
            new TwigFunction('owl_period_grid_filter_accounting_period_text', [$this, 'getAccountingPeriodText']),
            new TwigFunction('owl_period_grid_filter_generate_data_navigation', [$this, 'generateDataNavigation']),
        ];
    }

    /**
     * @param array<string, mixed> $activeCriteria
     *
     * @return array<string, array<string, mixed>>
     */
    public function generateLinkNavigation(string $field, string $route, array $activeCriteria): array
    {
        [$activeFieldCriteria, $navigation] = $this->getNavigationData($field, $activeCriteria);

        if (!$activeFieldCriteria || empty($navigation)) {
            return [];
        }

        return [
            'next' => [
                'path' => $this->generateUrl($route, $field, $activeCriteria, $activeFieldCriteria['type'], $navigation['next']),
                'is_disabled' => $navigation['next']['is_disabled'],
            ],
            'prev' => [
                'path' => $this->generateUrl($route, $field, $activeCriteria, $activeFieldCriteria['type'], $navigation['prev']),
                'is_disabled' => $navigation['prev']['is_disabled'],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $activeCriteria
     *
     * @return array<string, array<string, mixed>>
     */
    public function generateDataNavigation(string $field, array $activeCriteria): array
    {
        [$activeFieldCriteria, $navigation] = $this->getNavigationData($field, $activeCriteria);

        if (!$activeFieldCriteria || empty($navigation)) {
            return [];
        }

        $navigation['next']['query']['type'] = $activeFieldCriteria['type'] ?? null;
        $navigation['prev']['query']['type'] = $activeFieldCriteria['type'] ?? null;

        return $navigation;
    }

    /**
     * @param array<string, mixed> $activeCriteria
     */
    public function getAccountingPeriodText(string $field, array $activeCriteria): string
    {
        $activeFieldCriteria = $activeCriteria[$field] ?? null;

        return match ($activeFieldCriteria['type'] ?? null) {
            PeriodTypeEnum::TYPE_MONTH->value => $this->getNameMonth($activeFieldCriteria),
            PeriodTypeEnum::TYPE_QUARTER->value => $this->getNameQuarter($activeFieldCriteria),
            PeriodTypeEnum::TYPE_YEAR->value => $activeFieldCriteria['year'],
            default => $this->translator->trans('owl.ui.invoice.accounting_period_all')
        };
    }

    /**
     * @param array<string, mixed> $activeCriteria
     *
     * @return array{0: array<string, mixed>|null, 1: array<string, array<string, mixed>>}
     */
    private function getNavigationData(string $field, array $activeCriteria): array
    {
        $activeFieldCriteria = $activeCriteria[$field] ?? null;
        $navigation = [];

        if ($activeFieldCriteria && isset($activeFieldCriteria['type'])) {
            $navigation = match ($activeFieldCriteria['type']) {
                PeriodTypeEnum::TYPE_MONTH->value => $this->generateMonthNavigation($activeFieldCriteria),
                PeriodTypeEnum::TYPE_QUARTER->value => $this->generateQuarterNavigation($activeFieldCriteria),
                PeriodTypeEnum::TYPE_YEAR->value => $this->generateYearNavigation($activeFieldCriteria),
                default => [],
            };
        }

        return [$activeFieldCriteria, $navigation];
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<string, array<string, mixed>>
     */
    private function generateMonthNavigation(array $criteria): array
    {
        $nextMonth = $prevMonth = $criteria['month'];
        $nextYear = $prevYear = $criteria['year'];
        $maxNextYear = (int) date('Y', strtotime('+1 year'));
        $minPrevYear = (int) date('Y', strtotime('-5 year'));
        $navigation = [
            'next' => [
                'query' => [],
                'is_disabled' => false,
            ],
            'prev' => [
                'query' => [],
                'is_disabled' => false,
            ],
        ];

        if ($criteria['month'] == 12) {
            $nextMonth = 1;
            ++$nextYear;
            --$prevMonth;
        } elseif ($criteria['month'] == 1) {
            ++$nextMonth;
            $prevMonth = 12;
            --$prevYear;
        } else {
            ++$nextMonth;
            --$prevMonth;
        }

        if ($criteria['year'] == $maxNextYear && $nextMonth == 1) {
            $navigation['next'] = [
                'is_disabled' => true,
            ];
        } else {
            $navigation['next'] = [
                'query' => [
                    'month' => $nextMonth,
                    'year' => $nextYear,
                ],
                'is_disabled' => false,
            ];
        }

        if ($criteria['year'] == $minPrevYear && $prevMonth == 12) {
            $navigation['prev'] = [
                'is_disabled' => true,
            ];
        } else {
            $navigation['prev'] = [
                'query' => [
                    'month' => $prevMonth,
                    'year' => $prevYear,
                ],
                'is_disabled' => false,
            ];
        }

        return $navigation;
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<string, array<string, mixed>>
     */
    private function generateQuarterNavigation(array $criteria): array
    {
        $nextQuarter = $prevQuarter = $criteria['quarter'];
        $nextYear = $prevYear = $criteria['year'];
        $maxNextYear = (int) date('Y', strtotime('+1 year'));
        $minPrevYear = (int) date('Y', strtotime('-5 year'));
        $navigation = [
            'next' => [
                'query' => [],
                'is_disabled' => false,
            ],
            'prev' => [
                'query' => [],
                'is_disabled' => false,
            ],
        ];

        if ($criteria['quarter'] == 4) {
            $nextQuarter = 1;
            ++$nextYear;
            --$prevQuarter;
        } elseif ($criteria['quarter'] == 1) {
            ++$nextQuarter;
            $prevQuarter = 4;
            --$prevYear;
        } else {
            ++$nextQuarter;
            --$prevQuarter;
        }

        if ($criteria['year'] == $maxNextYear && $nextQuarter == 1) {
            $navigation['next'] = [
                'is_disabled' => true,
            ];
        } else {
            $navigation['next'] = [
                'query' => [
                    'quarter' => $nextQuarter,
                    'year' => $nextYear,
                ],
                'is_disabled' => false,
            ];
        }

        if ($criteria['year'] == $minPrevYear && $prevQuarter == 4) {
            $navigation['prev'] = [
                'is_disabled' => true,
            ];
        } else {
            $navigation['prev'] = [
                'query' => [
                    'quarter' => $prevQuarter,
                    'year' => $prevYear,
                ],
                'is_disabled' => false,
            ];
        }

        return $navigation;
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<string, array<string, mixed>>
     */
    private function generateYearNavigation(array $criteria): array
    {
        $nextYear = $prevYear = $criteria['year'];
        $maxNextYear = (int) date('Y', strtotime('+1 year'));
        $minPrevYear = (int) date('Y', strtotime('-5 year'));
        $navigation = [
            'next' => [
                'query' => [],
                'is_disabled' => false,
            ],
            'prev' => [
                'query' => [],
                'is_disabled' => false,
            ],
        ];

        if ($nextYear == $maxNextYear) {
            $navigation['next'] = [
                'is_disabled' => true,
            ];
        } else {
            $navigation['next'] = [
                'query' => [
                    'year' => ++$nextYear,
                ],
                'is_disabled' => false,
            ];
        }

        if ($prevYear == $minPrevYear) {
            $navigation['prev'] = [
                'is_disabled' => true,
            ];
        } else {
            $navigation['prev'] = [
                'query' => [
                    'year' => --$prevYear,
                ],
                'is_disabled' => false,
            ];
        }

        return $navigation;
    }

    /**
     * @param array<string, mixed> $activeCriteria
     * @param array<string, mixed> $navigation
     */
    private function generateUrl(string $route, string $field, array $activeCriteria, string $type, array $navigation): string
    {
        if ($navigation['is_disabled']) {
            return '#';
        }

        return $this->urlGenerator->generate($route, [
            'criteria' => array_replace_recursive($activeCriteria, [
                $field => array_merge(['type' => $type], $navigation['query']),
            ]),
        ]);
    }

    /**
     * @param array<string, mixed> $activeFieldCriteria
     */
    private function getNameMonth(array $activeFieldCriteria): string
    {
        $monthName = $this->monthTranslator->translate($activeFieldCriteria['month']);

        return $monthName . ' ' . $activeFieldCriteria['year'];
    }

    /**
     * @param array<string, mixed> $activeFieldCriteria
     */
    private function getNameQuarter(array $activeFieldCriteria): string
    {
        $quarterName = $this->translator->trans('owl.grid.filter.period.quarter_type_q' . $activeFieldCriteria['quarter']);

        return $quarterName . ' ' . $activeFieldCriteria['year'];
    }
}
