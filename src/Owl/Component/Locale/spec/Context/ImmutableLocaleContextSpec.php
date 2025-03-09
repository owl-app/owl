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

namespace spec\Owl\Component\Locale\Context;

use Owl\Component\Locale\Context\LocaleContextInterface;
use PhpSpec\ObjectBehavior;

final class ImmutableLocaleContextSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('pl_PL');
    }

    function it_is_a_locale_context(): void
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_gets_a_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn('pl_PL');
    }
}
