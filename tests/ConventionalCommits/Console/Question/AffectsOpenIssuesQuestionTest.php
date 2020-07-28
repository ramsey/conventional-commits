<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\AffectsOpenIssuesQuestion;
use Ramsey\Test\RamseyTestCase;

class AffectsOpenIssuesQuestionTest extends RamseyTestCase
{
    public function testQuestion(): void
    {
        $question = new AffectsOpenIssuesQuestion();

        $this->assertSame(
            'Does this change affect any open issues?',
            $question->getQuestion(),
        );
        $this->assertFalse($question->getDefault());
    }
}
