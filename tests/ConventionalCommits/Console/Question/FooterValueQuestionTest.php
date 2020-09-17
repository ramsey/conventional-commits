<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\FooterValueQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\Dev\Tools\TestCase;

class FooterValueQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new FooterValueQuestion('token-name');

        $this->assertSame(
            'Provide a value for the footer token-name',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsFooter(): void
    {
        $question = new FooterValueQuestion('token-name');
        $validator = $question->getValidator();

        /** @var Footer $footer */
        $footer = $validator('this is a footer value');

        $this->assertInstanceOf(Footer::class, $footer);
        $this->assertSame('token-name', $footer->getToken());
        $this->assertSame('this is a footer value', $footer->getValue());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new FooterValueQuestion('token-name');
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid footer value. Footer values may not contain other footers.');

        $validator("This footer value is invalid because\ntoken-name: it contains another footer");
    }

    public function testValidatorAcceptsUrlsAsValidValues(): void
    {
        $question = new FooterValueQuestion('token-name');
        $validator = $question->getValidator();

        /** @var Footer $footer */
        $footer = $validator('https://example.com/foo');

        $this->assertSame('https://example.com/foo', $footer->getValue());
    }

    public function testValidatorThrowsExceptionForEmptyValue(): void
    {
        $question = new FooterValueQuestion('token-name');
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid footer value. Footer values may not be empty.');

        $validator("\n");
    }
}
