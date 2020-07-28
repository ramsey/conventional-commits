<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\HasBreakingChangesQuestion;
use Ramsey\Test\RamseyTestCase;

class HasBreakingChangesQuestionTest extends RamseyTestCase
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
