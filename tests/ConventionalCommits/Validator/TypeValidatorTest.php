<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Validator\TypeValidator;
use Ramsey\Dev\Tools\TestCase;

class TypeValidatorTest extends TestCase
{
    /**
     * @param mixed[] $options
     *
     * @dataProvider provideTypeTestValues
     */
    public function testIsValid(string $testValue, bool $expectedResult, array $options = []): void
    {
        $validator = new TypeValidator();

        if ($options) {
            $validator->setConfiguration(new DefaultConfiguration($options));
        }

        $this->assertSame($expectedResult, $validator->isValid($testValue));
    }

    /**
     * @param mixed[] $options
     *
     * @dataProvider provideTypeTestValues
     */
    public function testIsValidOrException(
        string $testValue,
        bool $expectedResult,
        array $options = [],
        string $expectedError = ''
    ): void {
        $validator = new TypeValidator();

        if ($options) {
            $validator->setConfiguration(new DefaultConfiguration($options));
        }

        if ($expectedResult === false) {
            $this->expectException(InvalidValue::class);
            $this->expectExceptionMessage($expectedError);
        }

        $this->assertSame($expectedResult, $validator->isValidOrException($testValue));
    }

    /**
     * @return array<array{testValue: string, expectedResult: bool, options?: mixed[]}>
     */
    public function provideTypeTestValues(): array
    {
        $options = ['types' => ['FoO', 'baR', 'Baz']];

        return [
            [
                'testValue' => 'feat',
                'expectedResult' => true,
            ],
            [
                'testValue' => 'foo-bar',
                'expectedResult' => true,
            ],
            [
                'testValue' => 'foo bar',
                'expectedResult' => false,
                'options' => [],
                'expectedError' => "'foo bar' is not a valid type value",
            ],
            [
                'testValue' => 'feat',
                'expectedResult' => true,
                'options' => $options,
            ],
            [
                'testValue' => 'fix',
                'expectedResult' => true,
                'options' => $options,
            ],
            [
                'testValue' => 'foo',
                'expectedResult' => true,
                'options' => $options,
            ],
            [
                'testValue' => 'bAr',
                'expectedResult' => true,
                'options' => $options,
            ],
            [
                'testValue' => 'qux',
                'expectedResult' => false,
                'options' => $options,
                'expectedError' => "'qux' is not one of the valid types 'feat, fix, foo, bar, baz'",
            ],
            [
                'testValue' => 'BAZ',
                'expectedResult' => true,
                'options' => $options,
            ],
        ];
    }
}
