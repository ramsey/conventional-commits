<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Validator\EndMarkValidator;
use Ramsey\Test\TestCase;

use function sprintf;

class EndMarkValidatorTest extends TestCase
{
    /**
     * @dataProvider provideTestValues
     */
    public function testIsValid(?string $endMark, string $testValue, bool $expectedResult): void
    {
        $validator = new EndMarkValidator($endMark);

        $this->assertSame($expectedResult, $validator->isValid($testValue));
    }

    /**
     * @dataProvider provideTestValues
     */
    public function testIsValidOrException(?string $endMark, string $testValue, bool $expectedResult): void
    {
        $validator = new EndMarkValidator($endMark);

        if ($expectedResult === false) {
            $this->expectException(InvalidValue::class);
            $this->expectExceptionMessage(
                sprintf(
                    "'%s' does not end with the expected end mark '%s'",
                    $testValue,
                    $endMark,
                ),
            );
        }

        $this->assertSame($expectedResult, $validator->isValidOrException($testValue));
    }

    /**
     * @return array<array{endMark: string|null, testValue: string, expectedResult: bool}>
     */
    public function provideTestValues(): array
    {
        return [
            [
                'endMark' => '',
                'testValue' => 'This is a sentence',
                'expectedResult' => true,
            ],
            [
                'endMark' => '',
                'testValue' => 'This is a sentence.',
                'expectedResult' => false,
            ],
            [
                'endMark' => null,
                'testValue' => 'This is a sentence.',
                'expectedResult' => true,
            ],
            [
                'endMark' => null,
                'testValue' => 'This is a sentence!',
                'expectedResult' => true,
            ],
            [
                'endMark' => '.',
                'testValue' => 'This is a sentence.',
                'expectedResult' => true,
            ],
            [
                'endMark' => '.',
                'testValue' => 'This is a sentence',
                'expectedResult' => false,
            ],
            [
                'endMark' => ':',
                'testValue' => 'This is a sentence.',
                'expectedResult' => false,
            ],
            [
                'endMark' => '::',
                'testValue' => 'This is a sentence:',
                'expectedResult' => false,
            ],
            [
                'endMark' => '::',
                'testValue' => 'This is a sentence::',
                'expectedResult' => true,
            ],
            [
                'endMark' => '。',
                'testValue' => 'This is a sentence。',
                'expectedResult' => true,
            ],
            [
                'endMark' => '。',
                'testValue' => 'This is a sentence।',
                'expectedResult' => false,
            ],
            [
                'endMark' => '।',
                'testValue' => 'This is a sentence।',
                'expectedResult' => true,
            ],
            [
                'endMark' => '။',
                'testValue' => 'This is a sentence.',
                'expectedResult' => false,
            ],
            [
                'endMark' => '။',
                'testValue' => 'This is a sentence။',
                'expectedResult' => true,
            ],
            [
                'endMark' => '။',
                'testValue' => 'This is a sentence',
                'expectedResult' => false,
            ],
            [
                'endMark' => '',
                'testValue' => 'This is a sentence။',
                'expectedResult' => false,
            ],
            [
                'endMark' => '၏',
                'testValue' => 'This is a sentence၏',
                'expectedResult' => true,
            ],
            [
                'endMark' => '၏',
                'testValue' => 'This is a sentence',
                'expectedResult' => false,
            ],
            [
                'endMark' => '',
                'testValue' => 'This is a sentence၏',
                'expectedResult' => false,
            ],
            [
                'endMark' => '.......',
                'testValue' => 'This is a sentence၏',
                'expectedResult' => false,
            ],
            [
                'endMark' => '۔',
                'testValue' => 'This is a sentence۔',
                'expectedResult' => true,
            ],
            [
                'endMark' => '።',
                'testValue' => 'This is a sentence።',
                'expectedResult' => true,
            ],
        ];
    }
}
