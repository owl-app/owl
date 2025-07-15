<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Action\Account;

use Owl\Bundle\AdminBundle\Action\Account\ResetPasswordAction;
use Owl\Bundle\AdminBundle\Form\Model\PasswordReset;
use Owl\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Owl\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Owl\Component\Core\Factory\Http\RedirectResponseFactoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
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

final class ResetPasswordActionTest extends TestCase
{
    private ResetPasswordAction $action;

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

        $this->action = new ResetPasswordAction(
            $this->formFactory,
            $this->messageBus,
            $this->requestStack,
            $this->redirectResponseFactory,
            $this->twig,
        );
    }

    public function testRendersResetPasswordFormOnGetRequest(): void
    {
        // Arrange
        $token = 'reset-token-123';

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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
            ->with('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $formView,
            ])
            ->willReturn('<html>reset form</html>');

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>reset form</html>', $response->getContent());
    }

    public function testProcessesValidPasswordResetAndRedirects(): void
    {
        // Arrange
        $token = 'reset-token-123';
        $newPassword = 'new-password-123';

        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($newPassword);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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
            ->willReturn($passwordReset);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($command) use ($token, $newPassword) {
                return $command instanceof ResetPassword &&
                       $command->token === $token &&
                       $command->newPassword === $newPassword;
            }))
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
            ->with('success', 'owl.admin.password_reset.success');

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
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    public function testReturnsJsonResponseForAjaxRequestWithValidationErrors(): void
    {
        // Arrange
        $token = 'reset-token-123';

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('errors', $content);
        $this->assertSame('error', $content['status']);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function invalidTokenProvider(): array
    {
        return [
            'empty token' => ['token' => ''],
            'invalid token format' => ['token' => 'invalid-token'],
            'expired token' => ['token' => 'expired-token-123'],
        ];
    }

    #[DataProvider('invalidTokenProvider')]
    public function testHandlesInvalidTokens(string $token): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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
            ->with('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $formView,
            ])
            ->willReturn('<html>form</html>');

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testHandlesNullPasswordResetData(): void
    {
        // Arrange
        $token = 'reset-token-123';

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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

        $this->messageBus
            ->expects($this->never())
            ->method('dispatch');

        // Act & Assert
        $this->expectException(\Error::class);
        ($this->action)($this->request, $token);
    }

    public function testUsesCustomRedirectRouteWhenSpecified(): void
    {
        // Arrange
        $token = 'reset-token-123';
        $newPassword = 'new-password-123';

        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($newPassword);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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
            ->willReturn($passwordReset);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ResetPassword::class))
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
            ->willReturn(['redirect' => 'custom_redirect']);

        $this->request->attributes = $attributes;

        $redirectResponse = new Response();
        $this->redirectResponseFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->request, 'custom_redirect')
            ->willReturn($redirectResponse);

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertSame($redirectResponse, $response);
    }

    public function testRendersFormWithErrorsForNonAjaxInvalidSubmission(): void
    {
        // Arrange
        $token = 'reset-token-123';

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResetPasswordType::class)
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
            ->with('@OwlAdmin/security/reset_password.html.twig', [
                'form' => $formView,
            ])
            ->willReturn('<html>form with errors</html>');

        // Act
        $response = ($this->action)($this->request, $token);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>form with errors</html>', $response->getContent());
    }
}
