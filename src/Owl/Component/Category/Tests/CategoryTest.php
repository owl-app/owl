<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Category\Model;

use PHPUnit\Framework\TestCase;
use Owl\Component\Category\Model\Category;
use Owl\Component\Category\Model\CategoryInterface;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    public function testShouldImplementCategoryInterface(): void
    {
        self::assertInstanceOf(CategoryInterface::class, $this->category);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->category->getId());
    }

    public function testShouldHaveNoNameByDefault(): void
    {
        self::assertNull($this->category->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->category->setName('Books');
        self::assertSame('Books', $this->category->getName());
        $this->category->setName(null);
        self::assertNull($this->category->getName());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->category->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->category->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('-1 day');
        $this->category->setCreatedAt($date);
        self::assertSame($date, $this->category->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->category->setUpdatedAt($date);
        self::assertSame($date, $this->category->getUpdatedAt());
    }
}