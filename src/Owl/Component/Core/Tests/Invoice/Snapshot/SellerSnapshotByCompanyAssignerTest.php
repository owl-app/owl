<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Invoice\Snapshot;

use Owl\Component\Core\Factory\SellerFactoryInterface;
use Owl\Component\Core\Invoice\Snapshot\SellerSnapshotByCompanyAssigner;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SellerSnapshotByCompanyAssignerTest extends TestCase
{
    private SellerSnapshotByCompanyAssigner $assigner;

    private SnapshotAssignerInterface&MockObject $decorated;

    private SellerFactoryInterface&MockObject $sellerFactory;

    private Invoice&MockObject $invoice;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(SnapshotAssignerInterface::class);
        $this->sellerFactory = $this->createMock(SellerFactoryInterface::class);
        $this->invoice = $this->createMock(Invoice::class);

        $this->assigner = new SellerSnapshotByCompanyAssigner($this->decorated, $this->sellerFactory);
    }

    public function testAssignSetsSellerWhenCompanyChanged(): void
    {
        $company = $this->createMock(CompanyInterface::class);
        $seller = $this->createMock(SellerInterface::class);

        $this->invoice->method('isCompanyChanged')->willReturn(true);
        $this->invoice->method('getCompany')->willReturn($company);
        $this->sellerFactory->method('createFromCompany')->with($company)->willReturn($seller);

        $this->invoice->expects($this->once())->method('setSeller')->with($seller);
        $this->decorated->expects($this->once())->method('assign')->with($this->invoice);

        $this->assigner->assign($this->invoice);
    }

    public function testAssignDoesNothingWhenCompanyNotChanged(): void
    {
        $this->invoice->method('isCompanyChanged')->willReturn(false);

        $this->invoice->expects($this->never())->method('setSeller');
        $this->decorated->expects($this->never())->method('assign');

        $this->assigner->assign($this->invoice);
    }
}
