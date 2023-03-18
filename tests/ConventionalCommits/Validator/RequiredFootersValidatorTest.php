<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Validator\RequiredFootersValidator;
use Ramsey\Test\TestCase;

class RequiredFootersValidatorTest extends TestCase
{
    /**
     * @param mixed[] $testValue
     * @param array{typeCase?: string | null, types?: string[], scopeRequired?: bool, scopeCase?: string | null, scopes?: string[], descriptionCase?: string | null, descriptionEndMark?: string | null, bodyRequired?: bool, bodyWrapWidth?: int | null, requiredFooters?: string[]} $options
     *
     * @dataProvider provideTestValues
     */
    public function testIsValid(array $testValue, bool $expectedResult, array $options = []): void
    {
        $validator = new RequiredFootersValidator();

        if ($options) {
            $validator->setConfiguration(new DefaultConfiguration($options));
        }

        $this->assertSame($expectedResult, $validator->isValid($testValue));
    }

    /**
     * @param mixed[] $testValue
     * @param array{typeCase?: string | null, types?: string[], scopeRequired?: bool, scopeCase?: string | null, scopes?: string[], descriptionCase?: string | null, descriptionEndMark?: string | null, bodyRequired?: bool, bodyWrapWidth?: int | null, requiredFooters?: string[]} $options
     *
     * @dataProvider provideTestValues
     */
    public function testIsValidOrException(
        array $testValue,
        bool $expectedResult,
        array $options = [],
        string $expectedError = '',
    ): void {
        $validator = new RequiredFootersValidator();

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
     * @return array<array{testValue: mixed[], expectedResult: bool, options?: mixed[]}>
     */
    public function provideTestValues(): array
    {
        return [
            [
                'testValue' => [],
                'expectedResult' => true,
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a test value'),
                    new Footer('bar', 'a test value'),
                    new Footer('baz', 'a test value'),
                ],
                'expectedResult' => true,
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a test value'),
                    new Footer('bar', 'a test value'),
                    new Footer('baz', 'a test value'),
                ],
                'expectedResult' => true,
                'options' => ['requiredFooters' => []],
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a test value'),
                    new Footer('bar', 'a test value'),
                    new Footer('baz', 'a test value'),
                ],
                'expectedResult' => true,
                'options' => ['requiredFooters' => ['bar']],
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a test value'),
                    new Footer('bar', 'a test value'),
                    new Footer('baz', 'a test value'),
                ],
                'expectedResult' => true,
                'options' => ['requiredFooters' => ['bar', 'baz', 'foo']],
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a test value'),
                    new Footer('bar', 'a test value'),
                    new Footer('baz', 'a test value'),
                ],
                'expectedResult' => false,
                'options' => ['requiredFooters' => ['qux']],
                'expectedMessage' => 'Please provide the following required footers: qux.',
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a test value'),
                    new Footer('bar', 'a test value'),
                    new Footer('baz', 'a test value'),
                ],
                'expectedResult' => false,
                'options' => ['requiredFooters' => ['Bar', 'Quux', 'Baz', 'Foo', 'Qux']],
                'expectedMessage' => 'Please provide the following required footers: quux, qux.',
            ],
            [
                'testValue' => [],
                'expectedResult' => false,
                'options' => ['requiredFooters' => ['qux']],
                'expectedMessage' => 'Please provide the following required footers: qux.',
            ],
        ];
    }

    /**
     * @param mixed $testValue
     *
     * @dataProvider provideInvalidValues
     */
    public function testThrowsWhenValueIsInvalid($testValue): void
    {
        $validator = new RequiredFootersValidator();

        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('$value must be an array of ' . Footer::class);

        $validator->isValid($testValue);
    }

    /**
     * @return array<array{testValue: mixed}>
     */
    public function provideInvalidValues(): array
    {
        return [
            [
                'testValue' => 'an invalid value',
            ],
            [
                'testValue' => true,
            ],
            [
                'testValue' => 1234,
            ],
            [
                'testValue' => [
                    new Footer('foo', 'a value'),
                    'invalid',
                ],
            ],
        ];
    }
}
