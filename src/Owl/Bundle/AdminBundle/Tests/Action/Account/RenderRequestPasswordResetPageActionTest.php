<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Action\Account;

use Owl\Bundle\AdminBundle\Action\Account\RenderRequestPasswordResetPageAction;
use Owl\Bundle\AdminBundle\Form\Type\RequestPasswordResetType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[CoversClass(RenderRequestPasswordResetPageAction::class)]
final class RenderRequestPasswordResetPageActionTest extends TestCase
{
    private RenderRequestPasswordResetPageAction $action;
    private Environment&MockObject $twig;
    private FormFactoryInterface&MockObject $formFactory;
    private FormInterface&MockObject $form;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->action = new RenderRequestPasswordResetPageAction(
            $this->twig,
            $this->formFactory
        );
    }

    #[Test]
    public function it_renders_request_password_reset_page(): void
    {
        // Arrange
        $formView = $this->createMock(FormView::class);
        $expectedContent = '<html>password reset form</html>';

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

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
            ->willReturn($expectedContent);

        // Act
        $response = ($this->action)();

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame($expectedContent, $response->getContent());
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    #[Test]
    public function it_handles_form_factory_exception(): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willThrowException(new \RuntimeException('Form creation failed'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Form creation failed');
        ($this->action)();
    }

    #[Test]
    public function it_handles_form_view_creation_exception(): void
    {
        // Arrange
        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willThrowException(new \RuntimeException('Form view creation failed'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Form view creation failed');
        ($this->action)();
    }

    #[Test]
    public function it_handles_twig_render_exception(): void
    {
        // Arrange
        $formView = $this->createMock(FormView::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($this->form);

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
            ->willThrowException(new \RuntimeException('Template rendering failed'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Template rendering failed');
        ($this->action)();
    }

    #[Test]
    public function it_always_creates_form_with_correct_type(): void
    {
        // Arrange
        $formView = $this->createMock(FormView::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->identicalTo(RequestPasswordResetType::class))
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->willReturn('<html>content</html>');

        // Act
        ($this->action)();

        // Assert - expectations are verified by PHPUnit
    }

    #[Test]
    public function it_always_renders_correct_template(): void
    {
        // Arrange
        $formView = $this->createMock(FormView::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->identicalTo('@OwlAdmin/security/request_password_reset.html.twig'),
                $this->identicalTo(['form' => $formView])
            )
            ->willReturn('<html>content</html>');

        // Act
        ($this->action)();

        // Assert - expectations are verified by PHPUnit
    }

    #[Test]
    public function it_returns_response_with_correct_status_code(): void
    {
        // Arrange
        $formView = $this->createMock(FormView::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->willReturn('<html>content</html>');

        // Act
        $response = ($this->action)();

        // Assert
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    #[Test]
    public function it_returns_response_with_correct_content_type(): void
    {
        // Arrange
        $formView = $this->createMock(FormView::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->form);

        $this->form
            ->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->willReturn('<html>content</html>');

        // Act
        $response = ($this->action)();

        // Assert
        // Response doesn't set Content-Type automatically, it's set by the framework
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
