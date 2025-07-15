<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\Extension;

use Doctrine\Persistence\ObjectRepository;
use Owl\Bundle\AdminBundle\Form\Extension\LocationFieldsExtension;
use Owl\Bundle\CompanyBundle\Form\Type\CompanyType;
use Owl\Bundle\LocationBundle\Form\EventListener\BuildCountryFormSubscriber;
use Owl\Bundle\LocationBundle\Form\Type\CountryCodeChoiceType;
use Owl\Bundle\LocationBundle\Form\Type\ZoneChoiceType;
use Owl\Component\Location\Model\CountryCodeAwareInterface;
use Owl\Component\Location\Model\ProvinceCodeAwareInterface;
use Owl\Component\Location\Model\ZoneInterface;
use Owl\Component\Location\Repository\ZoneRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Test interface that combines both location interfaces for easier mocking
 */
interface TestableLocationData extends CountryCodeAwareInterface, ProvinceCodeAwareInterface
{
}

final class LocationFieldsExtensionTest extends TestCase
{
    private LocationFieldsExtension $extension;

    private BuildCountryFormSubscriber $buildCountryFormSubscriber;

    private ObjectRepository&MockObject $countryRepository;

    private FormFactoryInterface&MockObject $formFactory;

    private ZoneRepositoryInterface&MockObject $zoneRepository;

    private FormBuilderInterface&MockObject $formBuilder;

    private FormInterface&MockObject $form;

    private FormEvent&MockObject $formEvent;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(ObjectRepository::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->zoneRepository = $this->createMock(ZoneRepositoryInterface::class);
        $this->formBuilder = $this->createMock(FormBuilderInterface::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->formEvent = $this->createMock(FormEvent::class);

        // Create real instance of BuildCountryFormSubscriber with mocked dependencies
        $this->buildCountryFormSubscriber = new BuildCountryFormSubscriber(
            $this->countryRepository,
            $this->formFactory
        );

        $this->extension = new LocationFieldsExtension(
            $this->buildCountryFormSubscriber,
            $this->zoneRepository,
        );
    }

    public function testExtendsAbstractTypeExtension(): void
    {
        $this->assertInstanceOf(AbstractTypeExtension::class, $this->extension);
    }

    public function testExtendsCompanyType(): void
    {
        $extendedTypes = LocationFieldsExtension::getExtendedTypes();

        $this->assertContains(CompanyType::class, $extendedTypes);
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->buildCountryFormSubscriber);
    }

    public function testBuildsFormWithCountryCodeField(): void
    {
        // Arrange
        $options = [];

        $this->formBuilder
            ->expects($this->once())
            ->method('add')
            ->with(
                'countryCode',
                CountryCodeChoiceType::class,
                [
                    'label' => 'owl.form.address.country',
                    'enabled' => true,
                ]
            )
            ->willReturn($this->formBuilder);

        $this->formBuilder
            ->expects($this->once())
            ->method('addEventSubscriber')
            ->with($this->buildCountryFormSubscriber)
            ->willReturn($this->formBuilder);

        $this->formBuilder
            ->expects($this->exactly(3))
            ->method('addEventListener')
            ->willReturn($this->formBuilder);

        // Act
        $this->extension->buildForm($this->formBuilder, $options);
    }

    public function testAddsEventSubscriberToFormBuilder(): void
    {
        // Arrange
        $options = [];

        $this->formBuilder
            ->expects($this->once())
            ->method('add')
            ->willReturn($this->formBuilder);

        $this->formBuilder
            ->expects($this->once())
            ->method('addEventSubscriber')
            ->with($this->buildCountryFormSubscriber)
            ->willReturn($this->formBuilder);

        $this->formBuilder
            ->expects($this->exactly(3))
            ->method('addEventListener')
            ->willReturn($this->formBuilder);

        // Act
        $this->extension->buildForm($this->formBuilder, $options);
    }

    public function testAddsEventListenersToFormBuilder(): void
    {
        // Arrange
        $options = [];
        $eventListenerCalls = [];

        $this->formBuilder
            ->expects($this->once())
            ->method('add')
            ->willReturn($this->formBuilder);

        $this->formBuilder
            ->expects($this->once())
            ->method('addEventSubscriber')
            ->willReturn($this->formBuilder);

        $this->formBuilder
            ->expects($this->exactly(3))
            ->method('addEventListener')
            ->willReturnCallback(function ($event, $callback, $priority = 0) use (&$eventListenerCalls) {
                $eventListenerCalls[] = [$event, $callback, $priority];
                return $this->formBuilder;
            });

        // Act
        $this->extension->buildForm($this->formBuilder, $options);

        // Assert
        $this->assertCount(3, $eventListenerCalls);
        $this->assertEquals(FormEvents::PRE_SET_DATA, $eventListenerCalls[0][0]);
        $this->assertEquals(FormEvents::PRE_SUBMIT, $eventListenerCalls[1][0]);
        $this->assertEquals(FormEvents::SUBMIT, $eventListenerCalls[2][0]);
        $this->assertEquals(-100, $eventListenerCalls[0][2]);
        $this->assertEquals(-100, $eventListenerCalls[1][2]);
        $this->assertEquals(0, $eventListenerCalls[2][2]);
        $this->assertIsCallable($eventListenerCalls[0][1]);
        $this->assertIsCallable($eventListenerCalls[1][1]);
        $this->assertIsCallable($eventListenerCalls[2][1]);
    }

    public function testHandlesPreSetDataEventWithNullData(): void
    {
        // Arrange
        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $this->formEvent
            ->expects($this->never())
            ->method('getForm');

        // Act
        $this->triggerPreSetDataEvent(null);
    }

    public function testHandlesPreSetDataEventWithNullCountryCode(): void
    {
        // Arrange
        $data = $this->createMockDataWithCountryAndProvince(null, 'PL-01');

        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->never())
            ->method('getForm');

        // Act
        $this->triggerPreSetDataEvent($data);
    }

    public function testHandlesPreSetDataEventWithValidCountryCode(): void
    {
        // Arrange
        $countryCode = 'PL';
        $provinceCode = 'PL-01';
        $data = $this->createMockDataWithCountryAndProvince($countryCode, $provinceCode);
        $zones = [$this->createMock(ZoneInterface::class)];

        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->zoneRepository
            ->expects($this->once())
            ->method('findAllByCountryAndProvince')
            ->with($countryCode, $provinceCode)
            ->willReturn($zones);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with(
                'zone',
                ZoneChoiceType::class,
                [
                    'choices' => $zones,
                    'label' => 'owl.form.address.zone',
                    'auto_initialize' => false,
                    'required' => true,
                ]
            );

        // Act
        $this->triggerPreSetDataEvent($data);
    }

    public function testHandlesPreSubmitEventWithNullData(): void
    {
        // Arrange
        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $this->formEvent
            ->expects($this->never())
            ->method('getForm');

        // Act
        $this->triggerPreSubmitEvent(null);
    }

    public function testHandlesPreSubmitEventWithNonArrayData(): void
    {
        // Arrange
        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn('not an array');

        $this->formEvent
            ->expects($this->never())
            ->method('getForm');

        // Act
        $this->triggerPreSubmitEvent('not an array');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function invalidPreSubmitDataProvider(): array
    {
        return [
            'missing country code key' => [
                'data' => ['otherKey' => 'value'],
            ],
            'empty country code' => [
                'data' => ['countryCode' => ''],
            ],
            'null country code' => [
                'data' => ['countryCode' => null],
            ],
        ];
    }

    #[DataProvider('invalidPreSubmitDataProvider')]
    public function testHandlesPreSubmitEventWithInvalidData(array $data): void
    {
        // Arrange
        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->never())
            ->method('getForm');

        // Act
        $this->triggerPreSubmitEvent($data);
    }

    public function testHandlesPreSubmitEventWithValidData(): void
    {
        // Arrange
        $data = [
            'countryCode' => 'PL',
            'provinceCode' => 'PL-01',
        ];
        $zones = [$this->createMock(ZoneInterface::class)];

        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->zoneRepository
            ->expects($this->once())
            ->method('findAllByCountryAndProvince')
            ->with('PL', 'PL-01')
            ->willReturn($zones);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with(
                'zone',
                ZoneChoiceType::class,
                [
                    'choices' => $zones,
                    'label' => 'owl.form.address.zone',
                    'auto_initialize' => false,
                    'required' => true,
                ]
            );

        // Act
        $this->triggerPreSubmitEvent($data);
    }

    public function testHandlesPreSubmitEventWithMissingProvinceCode(): void
    {
        // Arrange
        $data = [
            'countryCode' => 'PL',
        ];
        $zones = [$this->createMock(ZoneInterface::class)];

        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->zoneRepository
            ->expects($this->once())
            ->method('findAllByCountryAndProvince')
            ->with('PL', null)
            ->willReturn($zones);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with(
                'zone',
                ZoneChoiceType::class,
                [
                    'choices' => $zones,
                    'label' => 'owl.form.address.zone',
                    'auto_initialize' => false,
                    'required' => true,
                ]
            );

        // Act
        $this->triggerPreSubmitEvent($data);
    }

    public function testHandlesSubmitEventWithValidDataAndProvinceCodeField(): void
    {
        // Arrange
        $data = $this->createMockDataWithCountryAndProvince('PL', 'PL-01');

        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('provinceCode')
            ->willReturn(true);

        $data
            ->expects($this->never())
            ->method('setProvinceCode');

        // Act
        $this->triggerSubmitEvent($data);
    }

    public function testHandlesSubmitEventWithValidDataAndNoProvinceCodeField(): void
    {
        // Arrange
        $data = $this->createMockDataWithCountryAndProvince('PL', 'PL-01');

        $this->formEvent
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->formEvent
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('provinceCode')
            ->willReturn(false);

        $data
            ->expects($this->once())
            ->method('setProvinceCode')
            ->with(null);

        // Act
        $this->triggerSubmitEvent($data);
    }

    public function testCreatesZoneChoiceFormRemovesZoneWhenProvinceCodeEmpty(): void
    {
        // Arrange
        $countryCode = 'PL';
        $provinceCode = null;

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('provinceCode')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('remove')
            ->with('zone');

        $this->form
            ->expects($this->never())
            ->method('add');

        $this->zoneRepository
            ->expects($this->never())
            ->method('findAllByCountryAndProvince');

        // Act
        $this->callCreateZoneChoiceForm($countryCode, $provinceCode);
    }

    public function testCreatesZoneChoiceFormWithZones(): void
    {
        // Arrange
        $countryCode = 'PL';
        $provinceCode = 'PL-01';
        $zones = [$this->createMock(ZoneInterface::class)];

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('provinceCode')
            ->willReturn(false);

        $this->zoneRepository
            ->expects($this->once())
            ->method('findAllByCountryAndProvince')
            ->with($countryCode, $provinceCode)
            ->willReturn($zones);

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with(
                'zone',
                ZoneChoiceType::class,
                [
                    'choices' => $zones,
                    'label' => 'owl.form.address.zone',
                    'auto_initialize' => false,
                    'required' => true,
                ]
            );

        // Act
        $this->callCreateZoneChoiceForm($countryCode, $provinceCode);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function createZoneChoiceFormDataProvider(): array
    {
        return [
            'empty string province code' => [
                'countryCode' => 'PL',
                'provinceCode' => '',
                'hasProvinceCode' => true,
                'shouldRemoveZone' => true,
            ],
            'null province code with province field' => [
                'countryCode' => 'PL',
                'provinceCode' => null,
                'hasProvinceCode' => true,
                'shouldRemoveZone' => true,
            ],
            'valid province code without province field' => [
                'countryCode' => 'PL',
                'provinceCode' => 'PL-01',
                'hasProvinceCode' => false,
                'shouldRemoveZone' => false,
            ],
            'valid province code with province field' => [
                'countryCode' => 'PL',
                'provinceCode' => 'PL-01',
                'hasProvinceCode' => false,
                'shouldRemoveZone' => false,
            ],
        ];
    }

    #[DataProvider('createZoneChoiceFormDataProvider')]
    public function testCreatesZoneChoiceFormWithVariousScenarios(
        string $countryCode,
        ?string $provinceCode,
        bool $hasProvinceCode,
        bool $shouldRemoveZone
    ): void {
        // Arrange
        $zones = [$this->createMock(ZoneInterface::class)];

        $this->form
            ->expects($this->once())
            ->method('has')
            ->with('provinceCode')
            ->willReturn($hasProvinceCode);

        if ($shouldRemoveZone) {
            $this->form
                ->expects($this->once())
                ->method('remove')
                ->with('zone');

            $this->form
                ->expects($this->never())
                ->method('add');

            $this->zoneRepository
                ->expects($this->never())
                ->method('findAllByCountryAndProvince');
        } else {
            $this->form
                ->expects($this->never())
                ->method('remove');

            $this->zoneRepository
                ->expects($this->once())
                ->method('findAllByCountryAndProvince')
                ->with($countryCode, $provinceCode)
                ->willReturn($zones);

            $this->form
                ->expects($this->once())
                ->method('add')
                ->with(
                    'zone',
                    ZoneChoiceType::class,
                    [
                        'choices' => $zones,
                        'label' => 'owl.form.address.zone',
                        'auto_initialize' => false,
                        'required' => true,
                    ]
                );
        }

        // Act
        $this->callCreateZoneChoiceForm($countryCode, $provinceCode);
    }

    public function testGetsSubscribedEventsFromBuildCountryFormSubscriber(): void
    {
        $events = BuildCountryFormSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $events);
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $events);
        $this->assertEquals('preSetData', $events[FormEvents::PRE_SET_DATA]);
        $this->assertEquals('preSubmit', $events[FormEvents::PRE_SUBMIT]);
    }

    private function triggerPreSetDataEvent(mixed $data): void
    {
        $preSetDataCallback = null;

        $this->formBuilder
            ->method('addEventListener')
            ->willReturnCallback(function ($event, $callback, $priority = 0) use (&$preSetDataCallback) {
                if ($event === FormEvents::PRE_SET_DATA) {
                    $preSetDataCallback = $callback;
                }
                return $this->formBuilder;
            });

        $this->extension->buildForm($this->formBuilder, []);

        $this->assertNotNull($preSetDataCallback, 'PRE_SET_DATA callback not found');

        // Call the callback
        $preSetDataCallback($this->formEvent);
    }

    private function triggerPreSubmitEvent(mixed $data): void
    {
        $preSubmitCallback = null;

        $this->formBuilder
            ->method('addEventListener')
            ->willReturnCallback(function ($event, $callback, $priority = 0) use (&$preSubmitCallback) {
                if ($event === FormEvents::PRE_SUBMIT) {
                    $preSubmitCallback = $callback;
                }
                return $this->formBuilder;
            });

        $this->extension->buildForm($this->formBuilder, []);

        $this->assertNotNull($preSubmitCallback, 'PRE_SUBMIT callback not found');

        // Call the callback
        $preSubmitCallback($this->formEvent);
    }

    private function triggerSubmitEvent(mixed $data): void
    {
        $submitCallback = null;

        $this->formBuilder
            ->method('addEventListener')
            ->willReturnCallback(function ($event, $callback, $priority = 0) use (&$submitCallback) {
                if ($event === FormEvents::SUBMIT) {
                    $submitCallback = $callback;
                }
                return $this->formBuilder;
            });

        $this->extension->buildForm($this->formBuilder, []);

        $this->assertNotNull($submitCallback, 'SUBMIT callback not found');

        // Call the callback
        $submitCallback($this->formEvent);
    }

    private function callCreateZoneChoiceForm(string $countryCode, ?string $provinceCode): void
    {
        $reflection = new \ReflectionClass($this->extension);
        $method = $reflection->getMethod('createZoneChoiceForm');
        $method->setAccessible(true);

        $method->invoke($this->extension, $countryCode, $provinceCode, $this->form);
    }

    private function createMockDataWithCountryAndProvince(?string $countryCode, ?string $provinceCode): CountryCodeAwareInterface&ProvinceCodeAwareInterface&MockObject
    {
        $mock = $this->createMock(TestableLocationData::class);

        $mock
            ->method('getCountryCode')
            ->willReturn($countryCode);

        $mock
            ->method('getProvinceCode')
            ->willReturn($provinceCode);

        return $mock;
    }
}
