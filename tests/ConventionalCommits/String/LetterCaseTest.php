<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\String;

use Ramsey\ConventionalCommits\String\LetterCase;
use Ramsey\Dev\Tools\TestCase;

class LetterCaseTest extends TestCase
{
    public function testConstantsAreAsExpected(): void
    {
        $this->assertSame('ada', LetterCase::CASE_ADA);
        $this->assertSame('camel', LetterCase::CASE_CAMEL);
        $this->assertSame('cobol', LetterCase::CASE_COBOL);
        $this->assertSame('dot', LetterCase::CASE_DOT);
        $this->assertSame('kebab', LetterCase::CASE_KEBAB);
        $this->assertSame('lower', LetterCase::CASE_LOWER);
        $this->assertSame('macro', LetterCase::CASE_MACRO);
        $this->assertSame('pascal', LetterCase::CASE_PASCAL);
        $this->assertSame('sentence', LetterCase::CASE_SENTENCE);
        $this->assertSame('snake', LetterCase::CASE_SNAKE);
        $this->assertSame('title', LetterCase::CASE_TITLE);
        $this->assertSame('train', LetterCase::CASE_TRAIN);
        $this->assertSame('upper', LetterCase::CASE_UPPER);
        $this->assertSame(
            [
                'ada',
                'camel',
                'cobol',
                'dot',
                'kebab',
                'lower',
                'macro',
                'pascal',
                'sentence',
                'snake',
                'title',
                'train',
                'upper',
            ],
            LetterCase::CASES,
        );
    }
}
