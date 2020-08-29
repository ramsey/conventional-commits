<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\Dev\Tools\TestCase;

class DescriptionTest extends TestCase
{
    /**
     * @return array<array{invalidDescription: string}>
     */
    public function provideInvalidDescription(): array
    {
        return [
            ['invalidDescription' => "foo\tbar"],
            ['invalidDescription' => "foo\r\nbar"],
            ['invalidDescription' => "foo\rbar"],
            ['invalidDescription' => "foo\nbar"],
        ];
    }

    /**
     * @return array<array{validDescription: string}>
     */
    public function provideValidDescription(): array
    {
        return [
            ['validDescription' => 'this is a valid description'],
            ['validDescription' => "this is a valid description\n"],
            ['validDescription' => 'This is a valid description'],
            ['validDescription' => 'THIS IS A VALID DESCRIPTION'],
            ['validDescription' => 'Thîs 1s @ välid déscriptiøn'],
        ];
    }

    /**
     * @dataProvider provideInvalidDescription
     */
    public function testThrowsExceptionForInvalidDescription(string $invalidDescription): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('Description may not contain any control characters');

        new Description($invalidDescription);
    }

    /**
     * @dataProvider provideValidDescription
     */
    public function testValidDescription(string $validDescription): void
    {
        $desc = new Description($validDescription);

        $trimmedValidDescription = trim($validDescription);

        $this->assertSame($trimmedValidDescription, $desc->toString());
        $this->assertSame($trimmedValidDescription, (string) $desc);
    }
}
