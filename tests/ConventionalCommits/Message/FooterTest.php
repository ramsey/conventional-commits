<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\Test\RamseyTestCase;

use function trim;

class FooterTest extends RamseyTestCase
{
    /**
     * @return array<array{invalidToken: string}>
     */
    public function provideInvalidToken(): array
    {
        return [
            ['invalidToken' => 'foo bar'],
            ['invalidToken' => 'FOO BAR'],
            ['invalidToken' => ' foobar'],
            ['invalidToken' => 'foobar '],
            ['invalidToken' => "foo\r\nbar"],
            ['invalidToken' => "foo\rbar"],
            ['invalidToken' => "foo\nbar"],
            ['invalidToken' => "\r\nfoobar"],
            ['invalidToken' => "\rfoobar"],
            ['invalidToken' => "\nfoobar"],
            ['invalidToken' => "foobar\r\n"],
            ['invalidToken' => "foobar\r"],
            ['invalidToken' => '-foobar'],
            ['invalidToken' => '_foobar'],
            ['invalidToken' => "foo\tbar"],
            ['invalidToken' => "foobar\t"],
            ['invalidToken' => "\tfoobar"],
            ['invalidToken' => ''],
        ];
    }

    /**
     * @return array<array{validToken: string}>
     */
    public function provideValidToken(): array
    {
        return [
            ['validToken' => 'foobar'],
            ['validToken' => 'FOObar'],
            ['validToken' => 'FOOBAR'],
            ['validToken' => 'foobar-'],
            ['validToken' => 'foobar_'],
            ['validToken' => 'foo-bar'],
            ['validToken' => 'foo_bar'],
            ['validToken' => "foobar\n"],
            ['validToken' => '123foobar'],
            ['validToken' => 'föôbár'],
            ['validToken' => 'BREAKING CHANGE'],
        ];
    }

    /**
     * @return array<array{invalidValue: string}>
     */
    public function provideInvalidValue(): array
    {
        return [
            ['invalidValue' => 'BREAKING CHANGE: a footer within a value'],
            ['invalidValue' => 'aToken: a footer within a value'],
            ['invalidValue' => "some value\nBREAKING CHANGE: a footer within a value\nMore of the value"],
            ['invalidValue' => "some value\rBREAKING CHANGE: a footer within a value\rMore of the value"],
            ['invalidValue' => "some value\r\nBREAKING CHANGE: a footer within a value\r\nMore of the value"],
            ['invalidValue' => "some value\naToken: a footer within a value\nMore of the value"],
            ['invalidValue' => "some value\raToken: a footer within a value\rMore of the value"],
            ['invalidValue' => "some value\r\naToken: a footer within a value\r\nMore of the value"],
            ['invalidValue' => ''],
        ];
    }

    /**
     * @return array<array{validValue: string}>
     */
    public function provideValidValue(): array
    {
        return [
            ['validValue' => 'this value is a BREAKING CHANGE: kind of value'],
            ['validValue' => 'We can also show aToken: in the value, and this works'],
            ['validValue' => "some value\n_foobar: invalid footer token passes the test\nMore of the value"],
            ['validValue' => "some value\r_foobar: invalid footer token passes the test\rMore of the value"],
            ['validValue' => "some value\r\n_foobar: invalid footer token passes the test\r\nMore of the value"],
            ['validValue' => "some value\n\nMore of the value"],
            ['validValue' => "some value\r\rMore of the value"],
            ['validValue' => "some value\r\n\r\nMore of the value"],
        ];
    }

    /**
     * @return array<array{invalidSeparator: string}>
     */
    public function provideInvalidSeparator(): array
    {
        return [
            ['invalidSeparator' => ';'],
            ['invalidSeparator' => ':'],
            ['invalidSeparator' => ' :'],
            ['invalidSeparator' => '#'],
            ['invalidSeparator' => '# '],
        ];
    }

    /**
     * @return array<array{validSeparator: string}>
     */
    public function provideValidSeparator(): array
    {
        return [
            ['validSeparator' => ': '],
            ['validSeparator' => ' #'],
        ];
    }

    /**
     * @return array<array{breakingChangeToken: string}>
     */
    public function provideBreakingChangeToken(): array
    {
        return [
            ['breakingChangeToken' => 'BREAKING CHANGE'],
            ['breakingChangeToken' => 'BREAKING-CHANGE'],
            ['breakingChangeToken' => 'breaking change'],
            ['breakingChangeToken' => 'breaking-change'],
            ['breakingChangeToken' => 'Breaking Change'],
            ['breakingChangeToken' => 'Breaking-Change'],
            ['breakingChangeToken' => 'bReAkInG cHaNgE'],
            ['breakingChangeToken' => 'bReAkInG-cHaNgE'],
        ];
    }

    /**
     * @dataProvider provideInvalidToken
     */
    public function testThrowsExceptionForInvalidToken(string $invalidToken): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage("Token '{$invalidToken}' is invalid");

        new Footer($invalidToken, 'a value');
    }

    /**
     * @dataProvider provideValidToken
     */
    public function testValidToken(string $validToken): void
    {
        $footer = new Footer($validToken, 'a value');

        $trimmedValidToken = trim($validToken);

        $this->assertSame($trimmedValidToken, $footer->getToken());
    }

    /**
     * @dataProvider provideInvalidValue
     */
    public function testThrowsExceptionForInvalidValue(string $invalidValue): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('Value is invalid');

        new Footer('aToken', $invalidValue);
    }

    /**
     * @dataProvider provideValidValue
     */
    public function testValidValue(string $validValue): void
    {
        $footer = new Footer('aToken', $validValue);

        $this->assertSame($validValue, $footer->getValue());
    }

    /**
     * @dataProvider provideInvalidSeparator
     */
    public function testThrowsExceptionForInvalidSeparator(string $invalidSeparator): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage(
            "Separator '{$invalidSeparator}' is invalid; expected one of [': ', ' #']",
        );

        new Footer('aToken', 'a value', $invalidSeparator);
    }

    /**
     * @dataProvider provideValidSeparator
     */
    public function testValidSeparator(string $validSeparator): void
    {
        $footer = new Footer('aToken', 'a value', $validSeparator);

        $this->assertSame($validSeparator, $footer->getSeparator());
    }

    public function testFooterWithNoPassedSeparator(): void
    {
        $footer = new Footer('a-token', 'this is a simple value');

        $this->assertSame('a-token: this is a simple value', $footer->toString());
        $this->assertSame('a-token: this is a simple value', (string) $footer);
    }

    public function testFooterWithColonSeparator(): void
    {
        $footer = new Footer('a-token', 'this is a simple value', Footer::SEPARATOR_COLON);

        $this->assertSame('a-token: this is a simple value', $footer->toString());
        $this->assertSame('a-token: this is a simple value', (string) $footer);
    }

    public function testFooterWithHashSeparator(): void
    {
        $footer = new Footer('FIXES', '1234', Footer::SEPARATOR_HASH);

        $this->assertSame('FIXES #1234', $footer->toString());
        $this->assertSame('FIXES #1234', (string) $footer);
    }

    /**
     * @dataProvider provideBreakingChangeToken
     */
    public function testFooterNormalizesBreakingChangeToken(string $breakingChangeToken): void
    {
        $footer = new Footer($breakingChangeToken, 'a value');

        $this->assertSame('BREAKING CHANGE', $footer->getToken());
    }
}
