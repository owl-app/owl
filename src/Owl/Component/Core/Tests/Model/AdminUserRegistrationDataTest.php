<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\AdminUserRegistrationData;
use Owl\Component\Core\Model\AdminUserRegistrationDataInterface;
use PHPUnit\Framework\TestCase;

final class AdminUserRegistrationDataTest extends TestCase
{
    private AdminUserRegistrationData $data;

    protected function setUp(): void
    {
        $this->data = new AdminUserRegistrationData();
    }

    public function testImplementsAdminUserRegistrationDataInterface(): void
    {
        self::assertInstanceOf(AdminUserRegistrationDataInterface::class, $this->data);
    }

    public function testCompanyIsMutable(): void
    {
        $this->data->setCompany('Acme Inc.');
        self::assertSame('Acme Inc.', $this->data->getCompany());
    }

    public function testFirstNameIsMutable(): void
    {
        $this->data->setFirstName('John');
        self::assertSame('John', $this->data->getFirstName());
    }

    public function testLastNameIsMutable(): void
    {
        $this->data->setLastName('Doe');
        self::assertSame('Doe', $this->data->getLastName());
    }

    public function testPhoneIsMutable(): void
    {
        $this->data->setPhone('123456789');
        self::assertSame('123456789', $this->data->getPhone());
    }

    public function testEmailIsMutable(): void
    {
        $this->data->setEmail('john@doe.com');
        self::assertSame('john@doe.com', $this->data->getEmail());
    }

    public function testCityIsMutable(): void
    {
        $this->data->setCity('Warsaw');
        self::assertSame('Warsaw', $this->data->getCity());
    }

    public function testStreetIsMutable(): void
    {
        $this->data->setStreet('Main St');
        self::assertSame('Main St', $this->data->getStreet());
    }

    public function testPostCodeIsMutable(): void
    {
        $this->data->setPostCode('00-001');
        self::assertSame('00-001', $this->data->getPostCode());
    }

    public function testNipIsMutable(): void
    {
        $this->data->setNip('1234567890');
        self::assertSame('1234567890', $this->data->getNip());
    }

    public function testStatusIsMutable(): void
    {
        $this->data->setStatus('accepted');
        self::assertSame('accepted', $this->data->getStatus());
    }

    public function testReasonRejectionIsMutable(): void
    {
        $this->data->setReasonRejection('Incomplete data');
        self::assertSame('Incomplete data', $this->data->getReasonRejection());
    }
}
