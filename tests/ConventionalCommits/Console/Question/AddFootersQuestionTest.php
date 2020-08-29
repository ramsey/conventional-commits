<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\AddFootersQuestion;
use Ramsey\Dev\Tools\TestCase;

class AddFootersQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new AddFootersQuestion();

        $this->assertSame(
            'Would you like to add any footers? (e.g., Signed-off-by, See-also)',
            $question->getQuestion(),
        );
        $this->assertFalse($question->getDefault());
    }
}
