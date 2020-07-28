<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Question;

use Ramsey\ConventionalCommits\Console\Question\TypeQuestion;
use Ramsey\ConventionalCommits\Exception\InvalidConsoleInput;
use Ramsey\ConventionalCommits\Message\Type;
use Ramsey\Test\RamseyTestCase;

class TypeQuestionTest extends RamseyTestCase
{
    public function testQuestion(): void
    {
        $question = new TypeQuestion();

        $this->assertSame(
            'What is the type of change you\'re committing? (e.g., feat, fix, etc.)',
            $question->getQuestion(),
        );
        $this->assertSame('feat', $question->getDefault());
    }

    public function testValidatorReturnsType(): void
    {
        $question = new TypeQuestion();
        $validator = $question->getValidator();

        /** @var Type $type */
        $type = $validator('feat');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame('feat', $type->toString());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new TypeQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid type. Please try again.');

        $validator('type name');
    }

    public function testAutocompleterCallback(): void
    {
        $question = new TypeQuestion();

        $this->assertSame(['feat', 'fix'], $question->getAutocompleterValues());
    }
}
