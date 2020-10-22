<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Console\Question\FooterTokenQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\Dev\Tools\TestCase;

class FooterTokenQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new FooterTokenQuestion();

        $this->assertSame(
            'To add a footer, provide a footer name, or press ENTER to skip (e.g., Signed-off-by)',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsNullForEmptyString(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(' '));
    }

    public function testValidatorReturnsNullForNull(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(null));
    }

    public function testValidatorReturnsTokenString(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        /** @var string $token */
        $token = $validator('token');

        $this->assertSame('token', $token);
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new FooterTokenQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid footer name. Token \'invalid token\' is invalid.');

        $validator('invalid token');
    }

    public function testAutocompleterCallbackWithNoConfiguredRequiredFooters(): void
    {
        $question = new FooterTokenQuestion();

        $this->assertNull($question->getAutocompleterValues());
    }

    public function testAutocompleterCallbackWithConfiguredRequiredFooters(): void
    {
        $question = new FooterTokenQuestion(new DefaultConfiguration([
            'requiredFooters' => ['foo-bar', 'See-also', 'Signed-off-by'],
        ]));

        $this->assertSame(['foo-bar', 'See-also', 'Signed-off-by'], $question->getAutocompleterValues());
    }
}
