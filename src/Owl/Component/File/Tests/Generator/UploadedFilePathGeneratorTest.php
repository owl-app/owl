<?php

declare(strict_types=1);

namespace Tests\Owl\Component\File\Generator;

use Owl\Component\File\Generator\UploadedFilePathGenerator;
use Owl\Component\File\Model\FileableInterface;
use Owl\Component\File\Model\FileInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedFilePathGeneratorTest extends TestCase
{
    public function testGenerateReturnsExpectedPath(): void
    {
        $generator = new UploadedFilePathGenerator();

        // Mocks
        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->method('guessExtension')->willReturn('jpg');

        $subjectMock = $this->getMockBuilder(FileableInterface::class)
            ->disableOriginalConstructor()
            ->setMockClassName('TestSubject')
            ->getMock();

        $date = new \DateTimeImmutable('2024-06-01 12:00:00');

        /** @var FileInterface&MockObject $imageMock */
        $imageMock = $this->createMock(FileInterface::class);
        $imageMock->method('getFile')->willReturn($fileMock);
        $imageMock->method('getFileSubject')->willReturn($subjectMock);
        $imageMock->method('getCreatedAt')->willReturn($date);

        $path = $generator->generate($imageMock);

        $this->assertMatchesRegularExpression(
            '#^2024/06/01/test_subject/[a-z0-9]{28}\.jpg$#',
            $path,
        );
    }

    public function testDirFromCamelCaseHandlesVariousCases(): void
    {
        $generator = new \ReflectionClass(UploadedFilePathGenerator::class);
        $method = $generator->getMethod('dirFromaCamelCase');
        $method->setAccessible(true);

        $instance = new UploadedFilePathGenerator();

        $this->assertSame('test_subject', $method->invoke($instance, 'TestSubject'));
        $this->assertSame('xml_http_request', $method->invoke($instance, 'XMLHttpRequest'));
        $this->assertSame('simple', $method->invoke($instance, 'Simple'));
        $this->assertSame('abc', $method->invoke($instance, 'ABC'));
    }

    public function testExpandPathReturnsExpectedFormat(): void
    {
        $generator = new \ReflectionClass(UploadedFilePathGenerator::class);
        $method = $generator->getMethod('expandPath');
        $method->setAccessible(true);

        $instance = new UploadedFilePathGenerator();

        $result = $method->invoke($instance, '2024/06/01', 'abcd1234.jpg', 'TestSubject');

        $this->assertSame('2024/06/01/test_subject/1234.jpg', $result);
    }
}
