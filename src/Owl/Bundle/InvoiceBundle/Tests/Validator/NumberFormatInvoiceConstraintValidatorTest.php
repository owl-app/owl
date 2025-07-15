<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\Validator;

use Owl\Bundle\InvoiceBundle\Validator\NumberFormatInvoiceConstraint;
use Owl\Bundle\InvoiceBundle\Validator\NumberFormatInvoiceConstraintValidator;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    public function testReturnsEarlyWhenExistingViolationFoundForSameProperty(): void
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

    public function testAddsViolationWhenInvoiceIsNull(): void
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

    public function testAddsViolationWhenInvoiceIsNotInvoiceInterface(): void
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

    public function testAddsViolationWhenInvoiceHasNoSerieAndEmptyValue(): void
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

    public function testAddsViolationWhenInvoiceHasNoSerieAndNullValue(): void
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

    public function testDoesNotAddViolationWhenInvoiceHasSerie(): void
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

    public function testDoesNotAddViolationWhenInvoiceHasNoSerieButValueIsProvided(): void
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

    public function testDoesNotAddViolationWhenInvoiceHasSerieAndValueIsProvided(): void
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

    public function testReturnsEarlyWhenViolationWithMatchingPropertyPathPrefixExists(): void
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

    public function testContinuesValidationWhenViolationWithNonMatchingPropertyPathExists(): void
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
