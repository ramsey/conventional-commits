<?php

declare(strict_types=1);

namespace Ramsey\Test;

use LogicException;
use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

use function is_string;
use function preg_replace;

/**
 * A text driver for spatie/phpunit-snapshot-assertions that ensures snapshots
 * are serialized and matched with lf line endings
 */
class WindowsSafeTextDriver implements Driver
{
    public function serialize(mixed $data): string
    {
        if (!is_string($data)) {
            throw new LogicException('Data must be a string');
        }

        // Save snapshot only with lf line endings.
        return (string) preg_replace('/\r\n/', "\n", $data);
    }

    public function extension(): string
    {
        return 'txt';
    }

    public function match(mixed $expected, mixed $actual): void
    {
        if (!is_string($expected)) {
            throw new LogicException('Expected must be a string');
        }

        // Make sure the expected string has lf line endings, so we can
        // compare accurately.
        $expected = (string) preg_replace('/\r\n/', "\n", $expected);

        Assert::assertEquals($expected, $this->serialize($actual));
    }
}
