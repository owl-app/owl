<?php

declare(strict_types=1);

namespace Owl\Component\Core\Translation;

use Owl\Component\Core\Context\AdminUserContextInterface;

final class MonthTranslator implements MonthTranslatorInterface
{
    public function __construct(
        private AdminUserContextInterface $adminUserContext,
    ) {
    }

    public function translate(int|string $month): string
    {
        $month = (int) $month;
        $locale = $this->adminUserContext->getUser()->getLocaleCode();

        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            null,
            null,
            'LLLL',
        );

        return $formatter->format(mktime(0, 0, 0, $month, 1));
    }
}
