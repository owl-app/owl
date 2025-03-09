<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

interface SuggestionStatusInterface
{
    public const STATUS_NEW = 'new';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_REALIZED = 'realized';

    public const STATUS_CANCELLED = 'cancelled';
}
