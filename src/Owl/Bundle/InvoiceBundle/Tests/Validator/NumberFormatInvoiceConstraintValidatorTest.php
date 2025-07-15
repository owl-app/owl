<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\Validator;

use InvalidArgumentException;
use Owl\Bundle\InvoiceBundle\Validator\NumberFormatInvoiceConstraint;
use Owl\Bundle\InvoiceBundle\Validator\NumberFormatInvoiceConstraintValidator;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[CoversClass(NumberFormatInvoiceConstraintValidator::class)]
class NumberFormatInvoiceConstraintValidatorTest extends TestCase
{
    private NumberFormatInvoiceConstraintValidator $validator;
    private TranslatorInterface&MockObject $translator;
    private ExecutionContextInterface&MockObject $context;
    private NumberFormatInvoiceConstraint $constraint;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->constraint = new NumberFormatInvoiceConstraint();
        $this->validator = new NumberFormatInvoiceConstraintValidator($this->translator);
        $this->validator->initialize($this->context);
    }

    #[Test]
    public function it_returns_early_when_existing_violation_found_for_same_property(): void
    {
        $propertyPath = 'number';
        $existingViolation = $this->createMock(ConstraintViolation::class);
        $existingViolation->method('getPropertyPath')->willReturn($propertyPath);
        
        $violations = new ConstraintViolationList([$existingViolation]);
        
        $this->context->method('getPropertyPath')->willReturn($propertyPath);
        $this->context->method('getViolations')->willReturn($violations);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate('test', $this->constraint);
    }

    #[Test]
    public function it_adds_violation_when_invoice_is_null(): void
    {
        $violations = new ConstraintViolationList();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn(null);
        
        $translatedMessage = 'Translated validation message';
        $this->translator->method('trans')->with($this->constraint->message)->willReturn($translatedMessage);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($translatedMessage)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate('test', $this->constraint);
    }

    #[Test]
    public function it_adds_violation_when_invoice_is_not_invoice_interface(): void
    {
        $violations = new ConstraintViolationList();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $notInvoice = new \stdClass();
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($notInvoice);
        
        $translatedMessage = 'Translated validation message';
        $this->translator->method('trans')->with($this->constraint->message)->willReturn($translatedMessage);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($translatedMessage)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate('test', $this->constraint);
    }

    #[Test]
    public function it_adds_violation_when_invoice_has_no_serie_and_empty_value(): void
    {
        $violations = new ConstraintViolationList();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($invoice);
        
        $invoice->method('getSerie')->willReturn(null);
        
        $translatedMessage = 'Translated validation message';
        $this->translator->method('trans')->with($this->constraint->message)->willReturn($translatedMessage);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($translatedMessage)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate('', $this->constraint);
    }

    #[Test]
    public function it_adds_violation_when_invoice_has_no_serie_and_null_value(): void
    {
        $violations = new ConstraintViolationList();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($invoice);
        
        $invoice->method('getSerie')->willReturn(null);
        
        $translatedMessage = 'Translated validation message';
        $this->translator->method('trans')->with($this->constraint->message)->willReturn($translatedMessage);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($translatedMessage)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate(null, $this->constraint);
    }

    #[Test]
    public function it_does_not_add_violation_when_invoice_has_serie(): void
    {
        $violations = new ConstraintViolationList();
        $invoice = $this->createMock(InvoiceInterface::class);
        $serie = $this->createMock(InvoiceSerieInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($invoice);
        
        $invoice->method('getSerie')->willReturn($serie);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate('', $this->constraint);
    }

    #[Test]
    public function it_does_not_add_violation_when_invoice_has_no_serie_but_value_is_provided(): void
    {
        $violations = new ConstraintViolationList();
        $invoice = $this->createMock(InvoiceInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($invoice);
        
        $invoice->method('getSerie')->willReturn(null);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate('INV-001', $this->constraint);
    }

    #[Test]
    public function it_does_not_add_violation_when_invoice_has_serie_and_value_is_provided(): void
    {
        $violations = new ConstraintViolationList();
        $invoice = $this->createMock(InvoiceInterface::class);
        $serie = $this->createMock(InvoiceSerieInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('number');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($invoice);
        
        $invoice->method('getSerie')->willReturn($serie);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate('INV-001', $this->constraint);
    }

    #[Test]
    public function it_returns_early_when_violation_with_matching_property_path_prefix_exists(): void
    {
        $propertyPath = 'invoice.number';
        $existingViolation = $this->createMock(ConstraintViolation::class);
        $existingViolation->method('getPropertyPath')->willReturn('invoice.number.format');
        
        $violations = new ConstraintViolationList([$existingViolation]);
        
        $this->context->method('getPropertyPath')->willReturn($propertyPath);
        $this->context->method('getViolations')->willReturn($violations);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate('test', $this->constraint);
    }

    #[Test]
    public function it_continues_validation_when_violation_with_non_matching_property_path_exists(): void
    {
        $propertyPath = 'invoice.number';
        $existingViolation = $this->createMock(ConstraintViolation::class);
        $existingViolation->method('getPropertyPath')->willReturn('invoice.description');
        
        $violations = new ConstraintViolationList([$existingViolation]);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn($propertyPath);
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($invoice);
        
        $invoice->method('getSerie')->willReturn(null);
        
        $translatedMessage = 'Translated validation message';
        $this->translator->method('trans')->with($this->constraint->message)->willReturn($translatedMessage);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($translatedMessage)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate('', $this->constraint);
    }
}
