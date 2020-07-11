<?php

declare(strict_types=1);

namespace Ramsey\Test\Conventional\Commit;

use Ramsey\Conventional\Commit\Scope;

class ScopeTest extends NounTestCase
{
    protected function getClassName(): string
    {
        return Scope::class;
    }
}
