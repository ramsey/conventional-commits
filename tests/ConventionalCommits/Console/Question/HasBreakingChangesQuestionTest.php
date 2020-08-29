<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\HasBreakingChangesQuestion;
use Ramsey\Dev\Tools\TestCase;

class HasBreakingChangesQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new HasBreakingChangesQuestion();

        $this->assertSame(
            'Are there any breaking changes?',
            $question->getQuestion(),
        );
        $this->assertFalse($question->getDefault());
    }
}
