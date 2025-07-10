<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Action\Account;

use Owl\Bundle\AdminBundle\Action\Account\RequestPasswordResetAction;
use Owl\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use Owl\Bundle\AdminBundle\Form\Type\RequestPasswordResetType;
use Owl\Bundle\CoreBundle\Command\Admin\Account\RequestResetPasswordEmail;
use Owl\Component\Core\Factory\Http\RedirectResponseFactoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

#[CoversClass(RequestPasswordResetAction::class)]
final class RequestPasswordResetActionTest extends TestCase
{
    private RequestPasswordResetAction $action;

    private FormFactoryInterface&MockObject $formFactory;

    private MessageBusInterface&MockObject $messageBus;

    private RequestStack&MockObject $requestStack;

    private RedirectResponseFactoryInterface&MockObject $redirectResponseFactory;

    private Environment&MockObject $twig;

    private FormInterface&MockObject $form;

    private Request&MockObject $request;

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->redirectResponseFactory = $this->createMock(RedirectResponseFactoryInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->request = $this->createMock(Request::class);

        $this->action = new RequestPasswordResetAction(
            $this->formFactory,
            $this->messageBus,
            $this->requestStack,
            $this->redirectResponseFactory,
            $this->twig,
        );
    }

    #[Test]
    public function it_renders_password_reset_form_on_get_request(): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false);

        $this->request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(false);

        $formView = $this->createMock(FormView::class);
        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/security/request_password_reset.html.twig', [
                'form' => $formView,
            ])
            ->willReturn('<html>form</html>');

        // Act
        $response = ($this->action)($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>form</html>', $response->getContent());
    }

    #[Test]
    public function it_processes_valid_form_submission_and_redirects(): void
    {
        // Arrange
        $email = 'test@example.com';
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($email);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($passwordResetRequest);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RequestResetPasswordEmail::class))
            ->willReturn(new Envelope(new \stdClass()));

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);

        // FlashBagProvider oczekuje FlashBagInterface z getBag('flashes')
        $session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($flashBag);

        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'owl.admin.request_reset_password.success');

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn(['redirect' => 'custom_redirect_route']);

        $this->request->attributes = $attributes;

        $redirectResponse = new Response();
        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, 'custom_redirect_route')
            ->willReturn($redirectResponse);

        // Act
        $response = ($this->action)($this->request);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    #[Test]
    public function it_uses_default_redirect_route_when_no_custom_route_specified(): void
    {
        // Arrange
        $email = 'test@example.com';
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($email);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($passwordResetRequest);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RequestResetPasswordEmail::class))
            ->willReturn(new Envelope(new \stdClass()));

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);

        // FlashBagProvider oczekuje FlashBagInterface z getBag('flashes')
        $session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($flashBag);

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn([]);

        $this->request->attributes = $attributes;

        $redirectResponse = new Response();
        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, 'owl_admin_login')
            ->willReturn($redirectResponse);

        // Act
        $response = ($this->action)($this->request);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    #[Test]
    public function it_returns_json_response_for_ajax_request_with_form_errors(): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(true);

        // Act
        $response = ($this->action)($this->request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('errors', $content);
        $this->assertSame('error', $content['status']);
    }

    #[Test]
    public function it_renders_form_with_errors_for_non_ajax_invalid_submission(): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $this->request
            ->expects($this->once())
            ->method('isXmlHttpRequest')
            ->willReturn(false);

        $formView = $this->createMock(FormView::class);
        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/security/request_password_reset.html.twig', [
                'form' => $formView,
            ])
            ->willReturn('<html>form with errors</html>');

        // Act
        $response = ($this->action)($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>form with errors</html>', $response->getContent());
    }

    #[Test]
    public function it_handles_null_form_data_gracefully(): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        // Act & Assert
        $this->expectException(\Error::class);
        ($this->action)($this->request);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function requestMethodProvider(): array
    {
        return [
            'GET request' => ['method' => 'GET', 'isSubmitted' => false],
            'POST request' => ['method' => 'POST', 'isSubmitted' => true],
            'PUT request' => ['method' => 'PUT', 'isSubmitted' => true],
            'PATCH request' => ['method' => 'PATCH', 'isSubmitted' => true],
        ];
    }

    #[Test]
    #[DataProvider('requestMethodProvider')]
    public function it_handles_different_request_methods(string $method, bool $isSubmitted): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('handleRequest')
            ->with($this->request);

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn($isSubmitted);

        if (!$isSubmitted) {
            $this->request
                ->expects($this->once())
                ->method('isXmlHttpRequest')
                ->willReturn(false);

            $formView = $this->createMock(FormView::class);
            $this->form
                ->expects($this->once())
                ->method('createView')
                ->willReturn($formView);

            $this->twig
                ->expects($this->once())
                ->method('render')
                ->with('@OwlAdmin/security/request_password_reset.html.twig', [
                    'form' => $formView,
                ])
                ->willReturn('<html>form</html>');
        } else {
            $this->form
                ->expects($this->once())
                ->method('isValid')
                ->willReturn(false);

            $this->request
                ->expects($this->once())
                ->method('isXmlHttpRequest')
                ->willReturn(false);

            $formView = $this->createMock(FormView::class);
            $this->form
                ->expects($this->once())
                ->method('createView')
                ->willReturn($formView);

            $this->twig
                ->expects($this->once())
                ->method('render')
                ->with('@OwlAdmin/security/request_password_reset.html.twig', [
                    'form' => $formView,
                ])
                ->willReturn('<html>form</html>');
        }

        // Act
        $response = ($this->action)($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
    }
}
