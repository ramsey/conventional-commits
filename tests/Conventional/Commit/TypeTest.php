<?php

declare(strict_types=1);

namespace Ramsey\Test\Conventional\Commit;

use Ramsey\Conventional\Commit\Type;

class TypeTest extends NounTestCase
{
    protected function getClassName(): string
    {
        return Type::class;
    }
}
