<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Validator\ScopeValidator;
use Ramsey\Test\TestCase;

class ScopeValidatorTest extends TestCase
{
    /**
     * @param array{typeCase?: string | null, types?: string[], scopeRequired?: bool, scopeCase?: string | null, scopes?: string[], descriptionCase?: string | null, descriptionEndMark?: string | null, bodyRequired?: bool, bodyWrapWidth?: int | null, requiredFooters?: string[]} $options
     *
     * @dataProvider provideScopeTestValues
     */
    public function testIsValid(
        string $testValue,
        bool $expectedResult,
        array $options = [],
        string $expectedError = '',
    ): void {
        $validator = new ScopeValidator();

        if ($options) {
            $validator->setConfiguration(new DefaultConfiguration($options));
        }

        $this->assertSame($expectedResult, $validator->isValid($testValue));
    }

    /**
     * @param array{typeCase?: string | null, types?: string[], scopeRequired?: bool, scopeCase?: string | null, scopes?: string[], descriptionCase?: string | null, descriptionEndMark?: string | null, bodyRequired?: bool, bodyWrapWidth?: int | null, requiredFooters?: string[]} $options
     *
     * @dataProvider provideScopeTestValues
     */
    public function testIsValidOrException(
        string $testValue,
        bool $expectedResult,
        array $options = [],
        string $expectedError = '',
    ): void {
        $validator = new ScopeValidator();

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
    public static function provideScopeTestValues(): array
    {
        $options = ['scopes' => ['FoO', 'baR', 'Baz']];

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
                'expectedError' => "'foo bar' is not a valid scope value",
            ],
            [
                'testValue' => 'feat',
                'expectedResult' => false,
                'options' => $options,
                'expectedError' => "'feat' is not one of the valid scopes 'foo, bar, baz'",
            ],
            [
                'testValue' => 'fix',
                'expectedResult' => false,
                'options' => $options,
                'expectedError' => "'fix' is not one of the valid scopes 'foo, bar, baz'",
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
                'expectedError' => "'qux' is not one of the valid scopes 'foo, bar, baz'",
            ],
            [
                'testValue' => 'BAZ',
                'expectedResult' => true,
                'options' => $options,
            ],
        ];
    }
}
