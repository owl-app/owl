<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\Validator;

use Owl\Bundle\InvoiceBundle\Validator\UniqueDefaultSerieConstraint;
use Owl\Bundle\InvoiceBundle\Validator\UniqueDefaultSerieConstraintValidator;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[CoversClass(UniqueDefaultSerieConstraintValidator::class)]
class UniqueDefaultSerieConstraintValidatorTest extends TestCase
{
    private UniqueDefaultSerieConstraintValidator $validator;
    private RepositoryInterface&MockObject $repository;
    private ExecutionContextInterface&MockObject $context;
    private UniqueDefaultSerieConstraint $constraint;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->constraint = new UniqueDefaultSerieConstraint();
        $this->validator = new UniqueDefaultSerieConstraintValidator($this->repository);
        $this->validator->initialize($this->context);
    }

    #[Test]
    public function it_returns_early_when_value_is_not_true(): void
    {
        $this->context->expects($this->never())->method('getPropertyPath');
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate(false, $this->constraint);
    }

    #[Test]
    public function it_returns_early_when_value_is_null(): void
    {
        $this->context->expects($this->never())->method('getPropertyPath');
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate(null, $this->constraint);
    }

    #[Test]
    public function it_returns_early_when_value_is_string(): void
    {
        $this->context->expects($this->never())->method('getPropertyPath');
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate('false', $this->constraint);
    }

    #[Test]
    public function it_returns_early_when_existing_violation_found_for_same_property(): void
    {
        $propertyPath = 'isDefault';
        $existingViolation = $this->createMock(ConstraintViolation::class);
        $existingViolation->method('getPropertyPath')->willReturn($propertyPath);
        
        $violations = new ConstraintViolationList([$existingViolation]);
        
        $this->context->method('getPropertyPath')->willReturn($propertyPath);
        $this->context->method('getViolations')->willReturn($violations);
        
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_returns_early_when_violation_with_matching_property_path_prefix_exists(): void
    {
        $propertyPath = 'serie.isDefault';
        $existingViolation = $this->createMock(ConstraintViolation::class);
        $existingViolation->method('getPropertyPath')->willReturn('serie.isDefault.validation');
        
        $violations = new ConstraintViolationList([$existingViolation]);
        
        $this->context->method('getPropertyPath')->willReturn($propertyPath);
        $this->context->method('getViolations')->willReturn($violations);
        
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_does_not_add_violation_when_no_existing_default_serie_found(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn(null);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_does_not_add_violation_when_existing_default_serie_is_same_as_validated_serie(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        $existingSerie = $this->createMock(InvoiceSerieInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        $validatedSerie->method('getId')->willReturn(1);
        $existingSerie->method('getId')->willReturn(1);
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn($existingSerie);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_adds_violation_when_different_default_serie_exists(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        $existingSerie = $this->createMock(InvoiceSerieInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        $validatedSerie->method('getId')->willReturn(1);
        $existingSerie->method('getId')->willReturn(2);
        $existingSerie->method('getFormat')->willReturn('INV-{YYYY}-{MM}-{###}');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn($existingSerie);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ format }}', 'INV-{YYYY}-{MM}-{###}')
            ->willReturnSelf();
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_handles_null_validated_serie_id(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        $existingSerie = $this->createMock(InvoiceSerieInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        $validatedSerie->method('getId')->willReturn(null);
        $existingSerie->method('getId')->willReturn(2);
        $existingSerie->method('getFormat')->willReturn('INV-{YYYY}-{MM}-{###}');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn($existingSerie);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ format }}', 'INV-{YYYY}-{MM}-{###}')
            ->willReturnSelf();
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_handles_null_existing_serie_id(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        $existingSerie = $this->createMock(InvoiceSerieInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        $validatedSerie->method('getId')->willReturn(1);
        $existingSerie->method('getId')->willReturn(null);
        $existingSerie->method('getFormat')->willReturn('INV-{YYYY}-{MM}-{###}');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn($existingSerie);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ format }}', 'INV-{YYYY}-{MM}-{###}')
            ->willReturnSelf();
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_continues_validation_when_violation_with_non_matching_property_path_exists(): void
    {
        $propertyPath = 'isDefault';
        $existingViolation = $this->createMock(ConstraintViolation::class);
        $existingViolation->method('getPropertyPath')->willReturn('format');
        
        $violations = new ConstraintViolationList([$existingViolation]);
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn($propertyPath);
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn(null);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_handles_different_invoice_types(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        $existingSerie = $this->createMock(InvoiceSerieInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('proforma');
        $validatedSerie->method('getId')->willReturn(1);
        $existingSerie->method('getId')->willReturn(2);
        $existingSerie->method('getFormat')->willReturn('PRO-{YYYY}-{###}');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'proforma'])
            ->willReturn($existingSerie);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ format }}', 'PRO-{YYYY}-{###}')
            ->willReturnSelf();
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_handles_null_invoice_type(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn(null);
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => null])
            ->willReturn(null);
        
        $this->context->expects($this->never())->method('buildViolation');
        
        $this->validator->validate(true, $this->constraint);
    }

    #[Test]
    public function it_handles_empty_format_in_existing_serie(): void
    {
        $violations = new ConstraintViolationList();
        $validatedSerie = $this->createMock(InvoiceSerieInterface::class);
        $existingSerie = $this->createMock(InvoiceSerieInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        
        $this->context->method('getPropertyPath')->willReturn('isDefault');
        $this->context->method('getViolations')->willReturn($violations);
        $this->context->method('getObject')->willReturn($validatedSerie);
        
        $validatedSerie->method('getInvoiceType')->willReturn('standard');
        $validatedSerie->method('getId')->willReturn(1);
        $existingSerie->method('getId')->willReturn(2);
        $existingSerie->method('getFormat')->willReturn('');
        
        $this->repository->method('findOneBy')
            ->with(['isDefault' => true, 'invoiceType' => 'standard'])
            ->willReturn($existingSerie);
        
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($this->constraint->message)
            ->willReturn($violationBuilder);
        
        $violationBuilder->expects($this->once())
            ->method('setParameter')
            ->with('{{ format }}', '')
            ->willReturnSelf();
        
        $violationBuilder->expects($this->once())->method('addViolation');
        
        $this->validator->validate(true, $this->constraint);
    }
}
