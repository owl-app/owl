<?php

declare(strict_types=1);

namespace Owl\Component\Core\Tests\Translation;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Translation\MonthTranslator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MonthTranslatorTest extends TestCase
{
    private AdminUserContextInterface&MockObject $adminUserContext;

    private AdminUserInterface&MockObject $user;

    private MonthTranslator $translator;

    protected function setUp(): void
    {
        $this->user = $this->getMockBuilder(AdminUserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adminUserContext = $this->getMockBuilder(AdminUserContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adminUserContext->method('getUser')->willReturn($this->user);

        $this->translator = new MonthTranslator($this->adminUserContext);
    }

    #[DataProvider('monthDataProvider')]
    public function testTranslateWithPolishLocale(int|string $month): void
    {
        $this->user->method('getLocaleCode')->willReturn('pl_PL');

        $translatedMonth = $this->translator->translate($month);

        $expectedMonths = [
            1 => 'styczeń',
            2 => 'luty',
            3 => 'marzec',
            4 => 'kwiecień',
            5 => 'maj',
            6 => 'czerwiec',
            7 => 'lipiec',
            8 => 'sierpień',
            9 => 'wrzesień',
            10 => 'październik',
            11 => 'listopad',
            12 => 'grudzień',
        ];

        $monthNumber = (int) $month;
        $this->assertEquals(mb_strtolower($expectedMonths[$monthNumber]), mb_strtolower($translatedMonth));
    }

    #[DataProvider('monthDataProvider')]
    public function testTranslateWithEnglishLocale(int|string $month): void
    {
        $this->user->method('getLocaleCode')->willReturn('en_US');

        $translatedMonth = $this->translator->translate($month);

        $expectedMonths = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $monthNumber = (int) $month;
        $this->assertEquals($expectedMonths[$monthNumber], $translatedMonth);
    }

    #[DataProvider('monthDataProvider')]
    public function testTranslateWithGermanLocale(int|string $month): void
    {
        $this->user->method('getLocaleCode')->willReturn('de_DE');

        $translatedMonth = $this->translator->translate($month);

        $expectedMonths = [
            1 => 'Januar',
            2 => 'Februar',
            3 => 'März',
            4 => 'April',
            5 => 'Mai',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'August',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Dezember',
        ];

        $monthNumber = (int) $month;
        $this->assertEquals($expectedMonths[$monthNumber], $translatedMonth);
    }

    /**
     * @return array<string, array<int|string>>
     */
    public static function monthDataProvider(): array
    {
        return [
            'January as int' => [1],
            'February as int' => [2],
            'March as int' => [3],
            'April as int' => [4],
            'May as int' => [5],
            'June as int' => [6],
            'July as int' => [7],
            'August as int' => [8],
            'September as int' => [9],
            'October as int' => [10],
            'November as int' => [11],
            'December as int' => [12],
            'January as string' => ['1'],
            'February as string' => ['2'],
            'March as string' => ['3'],
            'April as string' => ['4'],
            'May as string' => ['5'],
            'June as string' => ['6'],
            'July as string' => ['7'],
            'August as string' => ['8'],
            'September as string' => ['9'],
            'October as string' => ['10'],
            'November as string' => ['11'],
            'December as string' => ['12'],
        ];
    }

    // Edge cases for invalid months
    public function testTranslateWithInvalidMonth(): void
    {
        $this->user->method('getLocaleCode')->willReturn('en_US');

        $this->assertEquals($this->translator->translate(13), 'January');
    }

    public function testTranslateWithZeroMonth(): void
    {
        $this->user->method('getLocaleCode')->willReturn('en_US');

        $this->assertEquals($this->translator->translate(0), 'December');
    }

    public function testTranslateWithNegativeMonth(): void
    {
        $this->user->method('getLocaleCode')->willReturn('en_US');

        $this->assertEquals($this->translator->translate(-1), 'November');
    }
}