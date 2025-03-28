<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Setting\Model\Setting as BaseSetting;

class Setting extends BaseSetting
{
    protected ?string $descriptionLoginPage;

    protected ?string $descriptionDashboard;

    public function getDescriptionLoginPage(): ?string
    {
        return $this->descriptionLoginPage;
    }

    public function setDescriptionLoginPage(?string $descriptionLoginPage): void
    {
        $this->descriptionLoginPage = $descriptionLoginPage;
    }

    public function getDescriptionDashboard(): ?string
    {
        return $this->descriptionDashboard;
    }

    public function setDescriptionDashboard(?string $descriptionDashboard): void
    {
        $this->descriptionDashboard = $descriptionDashboard;
    }
}
