<?php

declare(strict_types=1);

namespace Owl\Bundle\SettingBundle\Tests\Action;

use InvalidArgumentException;
use Owl\Bridge\SyliusResource\Controller\AuthorizationCheckerInterface;
use Owl\Bridge\SyliusResource\Controller\EventDispatcherInterface;
use Owl\Bridge\SyliusResource\Controller\RedirectHandlerInterface;
use Owl\Bridge\SyliusResource\Controller\RequestConfiguration;
use Owl\Bridge\SyliusResource\Exception\InvalidResponseException;
use Owl\Bundle\SettingBundle\Action\SectionAction;
use Owl\Bundle\SettingBundle\Factory\SettingFormFactoryInterface;
use Owl\Component\Setting\Model\SettingInterface;
use Owl\Component\Setting\Storage\SettingStorageInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormView;
use Twig\Environment;

class SectionActionTest extends TestCase
{
    private RequestConfigurationFactoryInterface&MockObject $requestConfigurationFactory;
    private SettingFormFactoryInterface&MockObject $settingFormFactory;
    private SettingStorageInterface&MockObject $storage;
    private ViewHandlerInterface&MockObject $viewHandler;
    private RedirectHandlerInterface&MockObject $redirectHandler;
    private SectionAction $sectionAction;
    private Request&MockObject $request;
    private RequestConfiguration&MockObject $configuration;
    private FormInterface&MockObject $form;
    private SessionInterface&MockObject $session;
    private FlashBagInterface&MockObject $flashBag;
    private MetadataInterface&MockObject $metadata;
    private RepositoryInterface&MockObject $repository;
    private FactoryInterface&MockObject $factory;
    private ObjectManager&MockObject $manager;
    private AuthorizationCheckerInterface&MockObject $authorizationChecker;
    private EventDispatcherInterface&MockObject $eventDispatcher;
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->requestConfigurationFactory = $this->createMock(RequestConfigurationFactoryInterface::class);
        $this->settingFormFactory = $this->createMock(SettingFormFactoryInterface::class);
        $this->storage = $this->createMock(SettingStorageInterface::class);
        $this->viewHandler = $this->createMock(ViewHandlerInterface::class);
        $this->redirectHandler = $this->createMock(RedirectHandlerInterface::class);
        $this->request = $this->createMock(Request::class);
        $this->configuration = $this->createMock(RequestConfiguration::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);
        $this->metadata = $this->createMock(MetadataInterface::class);
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);

        $this->sectionAction = new SectionAction(
            $this->requestConfigurationFactory,
            $this->settingFormFactory,
            $this->storage,
            $this->viewHandler,
            $this->redirectHandler
        );

        $this->sectionAction->setMetadata($this->metadata);
        $this->sectionAction->setRepository($this->repository);
        $this->sectionAction->setFactory($this->factory);
        $this->sectionAction->setManager($this->manager);
        $this->sectionAction->setAuthorizationChecker($this->authorizationChecker);
        $this->sectionAction->setEventDispatcher($this->eventDispatcher);
        $this->sectionAction->setContainer($this->container);
    }

    public function testHandlesGetRequestSuccessfully(): void
    {
        $settingSection = 'test_section';
        $settings = [];
        $formData = [];
        $formView = $this->createMock(FormView::class);
        $response = new Response();

        $this->requestConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->metadata, $this->request)
            ->willReturn($this->configuration);

        $this->configuration
            ->method('getVars')
            ->willReturn(['setting_section' => $settingSection]);

        $this->storage
            ->expects($this->once())
            ->method('loadBySection')
            ->with($settingSection)
            ->willReturn($settings);

        $this->settingFormFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->configuration, $formData)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->method('isSubmitted')
            ->willReturn(false);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->configuration
            ->method('getTemplate')
            ->with('')
            ->willReturn('template.html.twig');

        $this->configuration
            ->method('isAjaxRequest')
            ->willReturn(false);

        $this->container
            ->method('has')
            ->willReturn(true);

        $this->container
            ->method('get')
            ->willReturn($this->createMock(Environment::class));

        $result = $this->sectionAction->__invoke($this->request);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testHandlesPutRequestWithValidForm(): void
    {
        $settingSection = 'test_section';
        $setting1 = $this->createMock(SettingInterface::class);
        $setting2 = $this->createMock(SettingInterface::class);
        $settings = [$setting1, $setting2];
        $formData = ['setting1' => 'value1', 'setting2' => 'value2'];
        $response = new Response();

        $setting1
            ->method('getName')
            ->willReturn('setting1');

        $setting1
            ->method('getValue')
            ->willReturn('value1');

        $setting2
            ->method('getName')
            ->willReturn('setting2');

        $setting2
            ->method('getValue')
            ->willReturn('value2');

        $this->requestConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->metadata, $this->request)
            ->willReturn($this->configuration);

        $this->configuration
            ->method('getVars')
            ->willReturn(['setting_section' => $settingSection]);

        $this->storage
            ->expects($this->once())
            ->method('loadBySection')
            ->with($settingSection)
            ->willReturn($settings);

        $this->settingFormFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->configuration, $formData)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->method('isValid')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        $this->request
            ->method('isMethod')
            ->with('PUT')
            ->willReturn(true);

        $this->storage
            ->expects($this->once())
            ->method('saveValues')
            ->with($settingSection, $formData, $settings);

        $this->request
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->method('getBag')
            ->with('flashes')
            ->willReturn($this->flashBag);

        $this->flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'owl_setting.settings_save_success');

        $this->redirectHandler
            ->expects($this->once())
            ->method('getRedirectHeaders')
            ->with($this->configuration, null)
            ->willReturn([]);

        $this->viewHandler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($response);

        $result = $this->sectionAction->__invoke($this->request);

        $this->assertInstanceOf(Response::class, $result);
    }

    public function testHandlesAjaxRequestWithInvalidForm(): void
    {
        $settingSection = 'test_section';
        $settings = [];
        $formData = [];
        $event = $this->createMock(ResourceControllerEvent::class);

        $this->requestConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->metadata, $this->request)
            ->willReturn($this->configuration);

        $this->configuration
            ->method('getVars')
            ->willReturn(['setting_section' => $settingSection]);

        $this->storage
            ->expects($this->once())
            ->method('loadBySection')
            ->with($settingSection)
            ->willReturn($settings);

        $this->settingFormFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->configuration, $formData)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->method('isValid')
            ->willReturn(false);

        $this->configuration
            ->method('isAjaxRequest')
            ->willReturn(true);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchAjaxValidationEvent')
            ->willReturn($event);

        $event
            ->method('isStopped')
            ->willReturn(false);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Event must be stopped');

        $this->sectionAction->__invoke($this->request);
    }

    public function testThrowsExceptionWhenSectionNotConfigured(): void
    {
        $this->requestConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->metadata, $this->request)
            ->willReturn($this->configuration);

        $this->configuration
            ->method('getVars')
            ->willReturn([]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Section param not exist in vars configuration route');

        $this->sectionAction->__invoke($this->request);
    }

    public function testMapsSettingsToArrayCorrectly(): void
    {
        $settingSection = 'test_section';
        $setting1 = $this->createMock(SettingInterface::class);
        $setting2 = $this->createMock(SettingInterface::class);
        $settings = [$setting1, $setting2];

        $setting1
            ->method('getName')
            ->willReturn('setting1');

        $setting1
            ->method('getValue')
            ->willReturn('value1');

        $setting2
            ->method('getName')
            ->willReturn('setting2');

        $setting2
            ->method('getValue')
            ->willReturn('value2');

        $this->requestConfigurationFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->metadata, $this->request)
            ->willReturn($this->configuration);

        $this->configuration
            ->method('getVars')
            ->willReturn(['setting_section' => $settingSection]);

        $this->storage
            ->expects($this->once())
            ->method('loadBySection')
            ->with($settingSection)
            ->willReturn($settings);

        $this->settingFormFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->configuration, ['setting1' => 'value1', 'setting2' => 'value2'])
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->method('isSubmitted')
            ->willReturn(false);

        $this->form
            ->method('createView')
            ->willReturn($this->createMock(FormView::class));

        $this->configuration
            ->method('getTemplate')
            ->with('')
            ->willReturn('template.html.twig');

        $this->configuration
            ->method('isAjaxRequest')
            ->willReturn(false);

        $this->container
            ->method('has')
            ->willReturn(true);

        $this->container
            ->method('get')
            ->willReturn($this->createMock(Environment::class));

        $result = $this->sectionAction->__invoke($this->request);

        $this->assertInstanceOf(Response::class, $result);
    }
} 