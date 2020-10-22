<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Converter;

use Jawira\CaseConverter\CaseConverter;
use Ramsey\ConventionalCommits\Converter\LetterCaseConverter;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\Dev\Tools\TestCase;

class LetterCaseConverterTest extends TestCase
{
    public function testThrowsWhenReceivingInvalidCase(): void
    {
        $caseConverter = new CaseConverter();

        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage("'foobar' is not a valid letter case");

        new LetterCaseConverter($caseConverter, 'foobar');
    }

    /**
     * @dataProvider provideConversionTestValues
     */
    public function testConvert(?string $case, string $testValue, string $expectedValue): void
    {
        $caseConverter = new CaseConverter();
        $converter = new LetterCaseConverter($caseConverter, $case);

        $this->assertSame($expectedValue, $converter->convert($testValue));
    }

    /**
     * @return array<array{case: string|null, testValue: string, expectedValue: string}>
     */
    public function provideConversionTestValues(): array
    {
        return [
            [
                'case' => 'ada',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'My_Name_Is_Bond',
            ],
            [
                'case' => 'camel',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'myNameIsBond',
            ],
            [
                'case' => 'cobol',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'MY-NAME-IS-BOND',
            ],
            [
                'case' => 'dot',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'my.name.is.bond',
            ],
            [
                'case' => 'kebab',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'my-name-is-bond',
            ],
            [
                'case' => 'lower',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'my name is bond',
            ],
            [
                'case' => 'macro',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'MY_NAME_IS_BOND',
            ],
            [
                'case' => 'pascal',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'MyNameIsBond',
            ],
            [
                'case' => 'sentence',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'My name is bond',
            ],
            [
                'case' => 'snake',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'my_name_is_bond',
            ],
            [
                'case' => 'title',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'My Name Is Bond',
            ],
            [
                'case' => 'train',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'My-Name-Is-Bond',
            ],
            [
                'case' => 'upper',
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'MY NAME IS BOND',
            ],
            [
                'case' => null,
                'testValue' => 'MY name IS bond',
                'expectedValue' => 'MY name IS bond',
            ],
        ];
    }
}
