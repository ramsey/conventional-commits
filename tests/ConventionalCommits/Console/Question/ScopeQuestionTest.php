<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\ScopeQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\Dev\Tools\TestCase;

class ScopeQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new ScopeQuestion();

        $this->assertSame(
            'What is the scope of this change (e.g., component or file name)? '
            . '<comment>(press enter to skip)</comment>',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsNullForEmptyString(): void
    {
        $question = new ScopeQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(' '));
    }

    public function testValidatorReturnsNullForNull(): void
    {
        $question = new ScopeQuestion();
        $validator = $question->getValidator();

        $this->assertNull($validator(null));
    }

    public function testValidatorReturnsScope(): void
    {
        $question = new ScopeQuestion();
        $validator = $question->getValidator();

        /** @var Scope $scope */
        $scope = $validator('component');

        $this->assertInstanceOf(Scope::class, $scope);
        $this->assertSame('component', $scope->toString());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new ScopeQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid scope. Please try again.');

        $validator('component name');
    }
}
