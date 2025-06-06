<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Invoice\Snapshot;

use Owl\Component\Core\Factory\BuyerFactoryInterface;
use Owl\Component\Core\Invoice\Snapshot\BuyerSnapshotByContractorAssigner;
use Owl\Component\Core\Model\ContractorInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BuyerSnapshotByContractorAssignerTest extends TestCase
{
    private BuyerSnapshotByContractorAssigner $assigner;

    private SnapshotAssignerInterface&MockObject $decorated;

    private BuyerFactoryInterface&MockObject $buyerFactory;

    private Invoice&MockObject $invoice;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(SnapshotAssignerInterface::class);
        $this->buyerFactory = $this->createMock(BuyerFactoryInterface::class);
        $this->invoice = $this->createMock(Invoice::class);

        $this->assigner = new BuyerSnapshotByContractorAssigner($this->decorated, $this->buyerFactory);
    }

    public function testAssignSetsBuyerWhenContractorChanged(): void
    {
        $contractor = $this->createMock(ContractorInterface::class);
        $buyer = $this->createMock(BuyerInterface::class);

        $this->invoice->method('isContractorChanged')->willReturn(true);
        $this->invoice->method('getContractor')->willReturn($contractor);
        $this->buyerFactory->method('createFromContractor')->with($contractor)->willReturn($buyer);

        $this->invoice->expects($this->once())->method('setBuyer')->with($buyer);
        $this->decorated->expects($this->once())->method('assign')->with($this->invoice);

        $this->assigner->assign($this->invoice);
    }

    public function testAssignDoesNothingWhenContractorNotChanged(): void
    {
        $this->invoice->method('isContractorChanged')->willReturn(false);

        $this->invoice->expects($this->never())->method('setBuyer');
        $this->decorated->expects($this->never())->method('assign');

        $this->assigner->assign($this->invoice);
    }
}
