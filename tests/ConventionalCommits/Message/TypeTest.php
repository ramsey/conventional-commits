<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Message\Type;

class TypeTest extends NounTestCase
{
    protected function getClassName(): string
    {
        return Type::class;
    }
}
