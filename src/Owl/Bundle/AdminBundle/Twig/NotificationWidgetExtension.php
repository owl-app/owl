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

namespace Owl\Bundle\AdminBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NotificationWidgetExtension extends AbstractExtension
{
    /** @var bool */
    private $areNotificationsEnabled;

    /** @var int */
    private $checkFrequency;

    public function __construct(bool $areNotificationsEnabled, int $checkFrequency)
    {
        $this->areNotificationsEnabled = $areNotificationsEnabled;
        $this->checkFrequency = $checkFrequency;
    }

    /**
     * @return list{TwigFunction}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'owl_render_notifications_widget',
                [$this, 'renderWidget'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }

    public function renderWidget(Environment $environment): string
    {
        if (!$this->areNotificationsEnabled) {
            return '';
        }

        return $environment->render('@OwlAdmin/Layout/_notification.html.twig', [
            'frequency' => $this->checkFrequency,
        ]);
    }
}
