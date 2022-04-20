<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message\Noun;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;
use Ramsey\ConventionalCommits\String\LetterCase;
use Ramsey\ConventionalCommits\Validator\LetterCaseValidator;
use Ramsey\ConventionalCommits\Validator\ScopeValidator;
use Ramsey\ConventionalCommits\Validator\TypeValidator;
use Ramsey\Test\TestCase;

use function array_pop;
use function explode;
use function trim;

abstract class NounTestCase extends TestCase
{
    /**
     * @return class-string<Noun>
     */
    abstract protected function getClassName(): string;

    /**
     * @return array<array{invalidNoun: string}>
     */
    public function provideInvalidNoun(): array
    {
        return [
            ['invalidNoun' => 'foo bar'],
            ['invalidNoun' => ' foobar'],
            ['invalidNoun' => 'foobar '],
            ['invalidNoun' => "foo\r\nbar"],
            ['invalidNoun' => "foo\rbar"],
            ['invalidNoun' => "foo\nbar"],
            ['invalidNoun' => "\r\nfoobar"],
            ['invalidNoun' => "\rfoobar"],
            ['invalidNoun' => "\nfoobar"],
            ['invalidNoun' => "foobar\r\n"],
            ['invalidNoun' => "foobar\r"],
            ['invalidNoun' => '-foobar'],
            ['invalidNoun' => '_foobar'],
            ['invalidNoun' => "foo\tbar"],
            ['invalidNoun' => "foobar\t"],
            ['invalidNoun' => "\tfoobar"],
        ];
    }

    /**
     * @return array<array{validNoun: string}>
     */
    public function provideValidNoun(): array
    {
        return [
            ['validNoun' => 'foobar'],
            ['validNoun' => 'FOObar'],
            ['validNoun' => 'FOOBAR'],
            ['validNoun' => 'foobar-'],
            ['validNoun' => 'foobar_'],
            ['validNoun' => 'foo-bar'],
            ['validNoun' => 'foo_bar'],
            ['validNoun' => "foobar\n"],
            ['validNoun' => '123foobar'],
            ['validNoun' => 'föôbár'],
        ];
    }

    /**
     * @dataProvider provideInvalidNoun
     */
    public function testThrowsExceptionForInvalidNoun(string $invalidNoun): void
    {
        $className = $this->getClassName();
        $classNameSegment = explode('\\', $className);
        $entity = array_pop($classNameSegment);

        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage(
            $entity . 's must contain only alphanumeric characters, underscores, and dashes',
        );

        new $className($invalidNoun);
    }

    /**
     * @dataProvider provideValidNoun
     */
    public function testValidNoun(string $validNoun): void
    {
        $className = $this->getClassName();

        /** @var Noun $noun */
        $noun = new $className($validNoun);

        $trimmedValidNoun = trim($validNoun);

        $this->assertSame($trimmedValidNoun, $noun->toString());
        $this->assertSame($trimmedValidNoun, (string) $noun);
    }

    /**
     * @dataProvider provideNounsForValidation
     */
    public function testValidatorsWithNoun(string $noun, bool $expectFailure): void
    {
        $className = $this->getClassName();

        /** @var Noun $noun */
        $noun = new $className($noun);

        $nouns = ['foo-bar', 'baz', 'qux'];
        $configuration = new DefaultConfiguration([
            'scopes' => $nouns,
            'types' => $nouns,
        ]);

        switch ($this->getClassName()) {
            case Scope::class:
                $validator = new ScopeValidator();

                break;
            case Type::class:
            default:
                $validator = new TypeValidator();

                break;
        }

        $validator->setConfiguration($configuration);
        $noun->addValidator($validator);
        $noun->addValidator(new LetterCaseValidator(LetterCase::CASE_KEBAB));

        if ($expectFailure === true) {
            $this->expectException(InvalidValue::class);
        }

        $this->assertTrue($noun->validate());
    }

    /**
     * @return array<array{noun: string, expectFailure: bool}>
     */
    public function provideNounsForValidation(): array
    {
        return [
            ['noun' => 'Foo-Bar', 'expectFailure' => true],
            ['noun' => 'foo-bar', 'expectFailure' => false],
            ['noun' => 'quux', 'expectFailure' => true],
            ['noun' => 'qux', 'expectFailure' => false],
        ];
    }
}
