<?php

declare(strict_types=1);

namespace Ramsey\Test;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * A base test case for common test functionality
 */
class RamseyTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @param class-string<T> $class
     * @param mixed ...$arguments
     *
     * @return T & MockInterface
     *
     * @template T
     */
    public function mockery(string $class, ...$arguments): MockInterface
    {
        /** @var T & MockInterface $mock */
        $mock = Mockery::mock($class, ...$arguments);

        return $mock;
    }
}
