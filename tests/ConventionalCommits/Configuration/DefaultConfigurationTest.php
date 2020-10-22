<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Configuration;

use Jawira\CaseConverter\CaseConverter;
use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Converter\LetterCaseConverter;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Validator\DefaultMessageValidator;
use Ramsey\ConventionalCommits\Validator\MessageValidator;
use Ramsey\Dev\Tools\TestCase;

use function json_encode;

class DefaultConfigurationTest extends TestCase
{
    public function testDefaultConfigurationValues(): void
    {
        $config = new DefaultConfiguration();
        $nullConverter = $config->getLetterCaseConverter(null);

        $this->assertNull($config->getDescriptionCase());
        $this->assertNull($config->getDescriptionEndMark());
        $this->assertNull($config->getScopeCase());
        $this->assertNull($config->getTypeCase());
        $this->assertSame([], $config->getScopes());
        $this->assertSame([], $config->getTypes());
        $this->assertSame([], $config->getRequiredFooters());
        $this->assertNull($nullConverter->getCase());
        $this->assertSame($nullConverter, $config->getLetterCaseConverter(null));
        $this->assertFalse($config->isScopeRequired());
        $this->assertFalse($config->isBodyRequired());
        $this->assertNull($config->getBodyWrapWidth());

        $this->assertSame(
            [
                'typeCase' => null,
                'types' => [],
                'scopeCase' => null,
                'scopeRequired' => false,
                'scopes' => [],
                'descriptionCase' => null,
                'descriptionEndMark' => null,
                'bodyRequired' => false,
                'bodyWrapWidth' => null,
                'requiredFooters' => [],
            ],
            $config->toArray(),
        );

        $this->assertSame(json_encode($config->toArray()), json_encode($config));
    }

    public function testSetConfigurationWithProvidedValues(): void
    {
        $converter = new LetterCaseConverter(new CaseConverter(), 'kebab');

        $config = new DefaultConfiguration([
            'typeCase' => 'kebab',
            'types' => ['foo', 'bar', 'baz-qux'],
            'scopeCase' => 'lower',
            'scopeRequired' => true,
            'scopes' => ['component', 'unit', 'foo'],
            'descriptionCase' => 'sentence',
            'descriptionEndMark' => '.',
            'bodyRequired' => true,
            'bodyWrapWidth' => 80,
            'requiredFooters' => ['Signed-off-by', 'Co-authored-by'],
        ]);

        $config->addLetterCaseConverter($converter);

        $this->assertSame('kebab', $config->getTypeCase());
        $this->assertSame(['foo', 'bar', 'baz-qux'], $config->getTypes());
        $this->assertSame('lower', $config->getScopeCase());
        $this->assertSame(['component', 'unit', 'foo'], $config->getScopes());
        $this->assertSame('sentence', $config->getDescriptionCase());
        $this->assertSame('.', $config->getDescriptionEndMark());
        $this->assertSame(['Signed-off-by', 'Co-authored-by'], $config->getRequiredFooters());
        $this->assertSame($converter, $config->getLetterCaseConverter('kebab'));
        $this->assertTrue($config->isScopeRequired());
        $this->assertTrue($config->isBodyRequired());
        $this->assertSame(80, $config->getBodyWrapWidth());

        $this->assertSame(
            [
                'typeCase' => 'kebab',
                'types' => ['foo', 'bar', 'baz-qux'],
                'scopeCase' => 'lower',
                'scopeRequired' => true,
                'scopes' => ['component', 'unit', 'foo'],
                'descriptionCase' => 'sentence',
                'descriptionEndMark' => '.',
                'bodyRequired' => true,
                'bodyWrapWidth' => 80,
                'requiredFooters' => ['Signed-off-by', 'Co-authored-by'],
            ],
            $config->toArray(),
        );

        $this->assertSame(json_encode($config->toArray()), json_encode($config));
    }

    /**
     * @param mixed[] $options
     *
     * @dataProvider provideInvalidData
     */
    public function testThrowsOnInvalidData(array $options, string $expectedMessage): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage($expectedMessage);

        new DefaultConfiguration($options);
    }

    /**
     * @return array<array{expectedMessage: string, options: mixed[]}>
     */
    public function provideInvalidData(): array
    {
        return [
            [
                'options' => ['descriptionCase' => 'foobar'],
                'expectedMessage' => "'foobar' is not a valid case for descriptionCase",
            ],
            [
                'options' => ['scopeCase' => 'baz'],
                'expectedMessage' => "'baz' is not a valid case for scopeCase",
            ],
            [
                'options' => ['typeCase' => 'qux'],
                'expectedMessage' => "'qux' is not a valid case for typeCase",
            ],
            [
                'options' => ['descriptionEndMark' => 'invalid'],
                'expectedMessage' => "'invalid' is not a valid punctuation character",
            ],
            [
                'options' => ['types' => ['foo', 'bar', 'invalid type', 'valid']],
                'expectedMessage' => "'invalid type' is not a valid type; types "
                    . 'may contain only alphanumeric characters, underscores, and dashes',
            ],
            [
                'options' => ['scopes' => ['foo', 'bar', 'invalid scope', 'valid']],
                'expectedMessage' => "'invalid scope' is not a valid scope; scopes "
                    . 'may contain only alphanumeric characters, underscores, and dashes',
            ],
            [
                'options' => ['requiredFooters' => ['Signed-off-by', 'BREAKING CHANGE', 'invalid footer', 'valid']],
                'expectedMessage' => "'invalid footer' is not a valid footer token; footer tokens "
                    . "may contain only alphanumeric characters and dashes or the phrase 'BREAKING CHANGE'",
            ],
        ];
    }

    public function testSetMessageValidator(): void
    {
        /** @var MessageValidator & MockInterface $validator */
        $validator = $this->mockery(MessageValidator::class);
        $config = new DefaultConfiguration();

        $config->setMessageValidator($validator);

        $this->assertSame($validator, $config->getMessageValidator());
    }

    public function testGetMessageValidatorSetsDefaultMessageValidatorWhenCalledForFirstTime(): void
    {
        $config = new DefaultConfiguration();
        $validator = $config->getMessageValidator();

        $this->assertInstanceOf(DefaultMessageValidator::class, $validator);
        $this->assertSame($validator, $config->getMessageValidator());
    }

    public function testTypesWithNonArray(): void
    {
        $config = new DefaultConfiguration([
            'types' => 'foo',
        ]);

        $this->assertSame(['foo'], $config->getTypes());
    }

    public function testScopesWithNonArray(): void
    {
        $config = new DefaultConfiguration([
            'scopes' => 'bar',
        ]);

        $this->assertSame(['bar'], $config->getScopes());
    }

    public function testRequiredFootersWithNonArray(): void
    {
        $config = new DefaultConfiguration([
            'requiredFooters' => 'baz',
        ]);

        $this->assertSame(['baz'], $config->getRequiredFooters());
    }
}
