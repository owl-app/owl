<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Formatter;

use Owl\Component\Core\Formatter\StringInflector;
use PHPUnit\Framework\TestCase;

final class StringInflectorTest extends TestCase
{
    public function testCodeToName(): void
    {
        $this->assertSame('Hello world', StringInflector::codeToName('hello_world'));
        $this->assertSame('Hello', StringInflector::codeToName('hello'));
        $this->assertSame('', StringInflector::codeToName(''));
        $this->assertSame('123 test', StringInflector::codeToName('123_test'));
    }

    public function testNameToCode(): void
    {
        $this->assertSame('hello_world', StringInflector::nameToCode('hello world'));
        $this->assertSame('hello_world', StringInflector::nameToCode('hello-world'));
        $this->assertSame('hello_world', StringInflector::nameToCode("hello'world"));
        $this->assertSame('hello', StringInflector::nameToCode('hello'));
        $this->assertSame('', StringInflector::nameToCode(''));
    }

    public function testNameToSlug(): void
    {
        $this->assertSame('hello-world', StringInflector::nameToSlug('hello world'));
        $this->assertSame('hello-world', StringInflector::nameToSlug('hello_world'));
        $this->assertSame('hello-world', StringInflector::nameToSlug('hello-world'));
        $this->assertSame('', StringInflector::nameToSlug(''));
        $this->assertSame('cafe', StringInflector::nameToSlug('café'));
    }

    public function testNameToLowercaseCode(): void
    {
        $this->assertSame('hello_world', StringInflector::nameToLowercaseCode('Hello World'));
        $this->assertSame('hello', StringInflector::nameToLowercaseCode('HELLO'));
        $this->assertSame('', StringInflector::nameToLowercaseCode(''));
    }

    public function testNameToUppercaseCode(): void
    {
        $this->assertSame('HELLO_WORLD', StringInflector::nameToUppercaseCode('Hello World'));
        $this->assertSame('HELLO', StringInflector::nameToUppercaseCode('hello'));
        $this->assertSame('', StringInflector::nameToUppercaseCode(''));
    }

    public function testNameToCamelCase(): void
    {
        $this->assertSame('helloWorld', StringInflector::nameToCamelCase('hello world'));
        $this->assertSame('helloWorld', StringInflector::nameToCamelCase('hello_world'));
        $this->assertSame('helloWorld', StringInflector::nameToCamelCase('Hello World'));
        $this->assertSame('', StringInflector::nameToCamelCase(''));
        $this->assertSame('caféTest', StringInflector::nameToCamelCase('café test'));
    }

    public function testNameWithSpecialCharactersToUpperCaseCode(): void
    {
        self::assertEquals('TEST?_VALUE!', StringInflector::nameToUppercaseCode('Test? value!'));
        self::assertEquals('test-value', StringInflector::nameToSlug('Test!%-value!'));
    }
}
