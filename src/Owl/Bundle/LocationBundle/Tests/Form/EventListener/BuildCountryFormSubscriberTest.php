<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Owl\Bundle\LocationBundle\Form\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Owl\Bundle\LocationBundle\Form\EventListener\BuildCountryFormSubscriber;
use Owl\Bundle\LocationBundle\Form\Type\ProvinceCodeChoiceType;
use Owl\Component\Location\Model\CountryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tests\Owl\Bundle\LocationBundle\Stub\CountryAndProviceInterface;

final class BuildCountryFormSubscriberTest extends TestCase
{
    /** @var RepositoryInterface<CountryInterface>&MockObject */
    private MockObject&RepositoryInterface $countryRepository;

    private FormFactoryInterface&MockObject $formFactory;

    private BuildCountryFormSubscriber $buildCountryFormSubscriber;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(RepositoryInterface::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->buildCountryFormSubscriber = new BuildCountryFormSubscriber($this->countryRepository, $this->formFactory);
    }

    public function testImplementsAnEventSubscriber(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->buildCountryFormSubscriber);
    }

    public function testSubscribesToEvent(): void
    {
        $this->assertSame([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ], BuildCountryFormSubscriber::getSubscribedEvents());
    }

    public function testAddsProvincesOnPreSetData(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var CountryAndProviceInterface&MockObject $countryAndProvinceAware */
        $countryAndProvinceAware = $this->createMock(CountryAndProviceInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($countryAndProvinceAware);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $countryAndProvinceAware->expects($this->once())->method('getCountryCode')->willReturn('IE');
        $country->expects($this->once())->method('hasProvinces')->willReturn(true);
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'IE'])->willReturn($country);
        $this->formFactory
            ->expects($this->once())
            ->method('createNamed')
            ->with('provinceCode', ProvinceCodeChoiceType::class, null, $this->callback(function (array $options) use ($country) {
                return is_array($options) &&
                    isset($options['country']) &&
                    $options['country'] === $country
                ;
            }))
            ->willReturn($provinceForm)
        ;
        $form->expects($this->once())->method('add')->with($provinceForm)->willReturn($form);

        $this->buildCountryFormSubscriber->preSetData($event);
    }

    public function testNotAddsProvincesOnPreSetData(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var CountryInterface&MockObject $country */
        /** @var CountryAndProviceInterface&MockObject $countryAndProvinceAware */
        $countryAndProvinceAware = $this->createMock(CountryAndProviceInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($countryAndProvinceAware);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $countryAndProvinceAware->expects($this->once())->method('getCountryCode')->willReturn('IE');
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'IE'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(false);
        $this->formFactory
            ->expects($this->never())
            ->method('createNamed');

        $this->buildCountryFormSubscriber->preSetData($event);
    }

    public function testAddsProvincesOnPreSubmit(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $event->expects($this->once())->method('getData')->willReturn(['countryCode' => 'FR']);
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'FR'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(true);
        $this->formFactory
            ->expects($this->once())
            ->method('createNamed')
            ->with('provinceCode', ProvinceCodeChoiceType::class, null, $this->callback(function (array $options) use ($country) {
                return is_array($options) &&
                    isset($options['country']) &&
                    $options['country'] === $country
                ;
            }))
            ->willReturn($provinceForm)
        ;
        $form->expects($this->once())->method('add')->with($provinceForm)->willReturn($form);

        $this->buildCountryFormSubscriber->preSubmit($event);
    }

    public function testAddsProvinceNameFieldOnPreSubmitIfCountryDoesNotHaveProvinces(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $provinceForm */
        $provinceForm = $this->createMock(FormInterface::class);
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $event->expects($this->once())->method('getData')->willReturn(['countryCode' => 'US']);
        $event->expects($this->once())->method('getForm')->willReturn($form);
        $this->countryRepository->expects($this->once())->method('findOneBy')->with(['code' => 'US'])->willReturn($country);
        $country->expects($this->once())->method('hasProvinces')->willReturn(false);
        $this->formFactory
            ->expects($this->never())
            ->method('createNamed');

        $this->buildCountryFormSubscriber->preSubmit($event);
    }
}
