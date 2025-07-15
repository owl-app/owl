<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\Validator;

use Owl\Bundle\InvoiceBundle\Validator\UniqueDefaultSerieConstraint;
use Owl\Bundle\InvoiceBundle\Validator\UniqueDefaultSerieConstraintValidator;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

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

    public function testReturnsEarlyWhenValueIsNotTrue(): void
    {
        $this->context->expects($this->never())->method('getPropertyPath');
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate(false, $this->constraint);
    }

    public function testReturnsEarlyWhenValueIsNull(): void
    {
        $this->context->expects($this->never())->method('getPropertyPath');
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate(null, $this->constraint);
    }

    public function testReturnsEarlyWhenValueIsString(): void
    {
        $this->context->expects($this->never())->method('getPropertyPath');
        $this->repository->expects($this->never())->method('findOneBy');
        
        $this->validator->validate('false', $this->constraint);
    }

    public function testReturnsEarlyWhenExistingViolationFoundForSameProperty(): void
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

    public function testReturnsEarlyWhenViolationWithMatchingPropertyPathPrefixExists(): void
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

    public function testDoesNotAddViolationWhenNoExistingDefaultSerieFound(): void
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

    public function testDoesNotAddViolationWhenExistingDefaultSerieIsSameAsValidatedSerie(): void
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

    public function testAddsViolationWhenDifferentDefaultSerieExists(): void
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

    public function testHandlesNullValidatedSerieId(): void
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

    public function testHandlesNullExistingSerieId(): void
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

    public function testContinuesValidationWhenViolationWithNonMatchingPropertyPathExists(): void
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

    public function testHandlesDifferentInvoiceTypes(): void
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

    public function testHandlesNullInvoiceType(): void
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

    public function testHandlesEmptyFormatInExistingSerie(): void
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
