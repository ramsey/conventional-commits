<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Message\Noun;
use Ramsey\Dev\Tools\TestCase;

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

        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage(
            'Nouns must contain only alphanumeric characters, underscores, and dashes',
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
}
