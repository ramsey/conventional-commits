<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Validator\LetterCaseValidator;
use Ramsey\Dev\Tools\TestCase;

use function sprintf;

class LetterCaseValidatorTest extends TestCase
{
    /**
     * @dataProvider provideTestValues
     */
    public function testIsValid(string $case, string $testValue, bool $expectedResult): void
    {
        $validator = new LetterCaseValidator($case);

        $this->assertSame($expectedResult, $validator->isValid($testValue));
    }

    /**
     * @dataProvider provideTestValues
     */
    public function testIsValidOrException(string $case, string $testValue, bool $expectedResult): void
    {
        $validator = new LetterCaseValidator($case);

        if ($expectedResult === false) {
            $this->expectException(InvalidValue::class);
            $this->expectExceptionMessage(sprintf(
                "'%s' is not formatted in %s case",
                $testValue,
                $case,
            ));
        }

        $this->assertSame($expectedResult, $validator->isValidOrException($testValue));
    }

    /**
     * @return array<array{case: string, testValue: string, expectedResult: bool}>
     */
    public function provideTestValues(): array
    {
        return [
            [
                'case' => 'ada',
                'testValue' => 'My_Name_Is_Bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'ada',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'camel',
                'testValue' => 'myNameIsBond',
                'expectedResult' => true,
            ],
            [
                'case' => 'camel',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'cobol',
                'testValue' => 'MY-NAME-IS-BOND',
                'expectedResult' => true,
            ],
            [
                'case' => 'cobol',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'dot',
                'testValue' => 'my.name.is.bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'dot',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'kebab',
                'testValue' => 'my-name-is-bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'kebab',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'lower',
                'testValue' => 'my name is bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'lower',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'macro',
                'testValue' => 'MY_NAME_IS_BOND',
                'expectedResult' => true,
            ],
            [
                'case' => 'macro',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'pascal',
                'testValue' => 'MyNameIsBond',
                'expectedResult' => true,
            ],
            [
                'case' => 'pascal',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'sentence',
                'testValue' => 'My name is bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'sentence',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'snake',
                'testValue' => 'my_name_is_bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'snake',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'title',
                'testValue' => 'My Name Is Bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'title',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'train',
                'testValue' => 'My-Name-Is-Bond',
                'expectedResult' => true,
            ],
            [
                'case' => 'train',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
            [
                'case' => 'upper',
                'testValue' => 'MY NAME IS BOND',
                'expectedResult' => true,
            ],
            [
                'case' => 'upper',
                'testValue' => 'MY name IS bond',
                'expectedResult' => false,
            ],
        ];
    }
}
