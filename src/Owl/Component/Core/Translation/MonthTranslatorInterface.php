<?php

declare(strict_types=1);

namespace Owl\Component\Core\Translation;

interface MonthTranslatorInterface
{
    public function translate(int|string $month): string;
}
