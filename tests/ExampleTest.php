<?php

declare(strict_types=1);

namespace Ramsey\Test\Captainhook\Conventional;

use Mockery\MockInterface;
use Ramsey\Captainhook\Conventional\Example;

class ExampleTest extends RamseyTestCase
{
    public function testGreet(): void
    {
        /** @var Example & MockInterface $example */
        $example = $this->mockery(Example::class);
        $example->shouldReceive('greet')->passthru();

        $this->assertSame('Hello, Friends!', $example->greet('Friends'));
    }
}
