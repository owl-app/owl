<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Status\Model\Status;

class SuggestionStatus extends Status implements SuggestionStatusInterface
{
    /**
     * @return array{new: 'owl.ui.status_new', in_progress: 'owl.ui.status_in_progress', realized: 'owl.ui.status_realized', cancelled: 'owl.ui.status_cancelled'}
     */
    public function getStatusesLabels(): array
    {
        return [
            self::STATUS_NEW => 'owl.ui.status_new',
            self::STATUS_IN_PROGRESS => 'owl.ui.status_in_progress',
            self::STATUS_REALIZED => 'owl.ui.status_realized',
            self::STATUS_CANCELLED => 'owl.ui.status_cancelled',
        ];
    }
}
