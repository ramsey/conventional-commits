<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Message\Scope;

class ScopeTest extends NounTestCase
{
    protected function getClassName(): string
    {
        return Scope::class;
    }
}
