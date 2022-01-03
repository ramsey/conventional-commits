<?php

declare(strict_types=1);

namespace Ramsey\Test;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;

use function preg_replace;

/**
 * A text driver for spatie/phpunit-snapshot-assertions that ensures snapshots
 * are serialized and matched with lf line endings
 */
class WindowsSafeTextDriver implements Driver
{
    /**
     * @inheritDoc
     */
    public function serialize($data): string
    {
        /** @var string $stringData */
        $stringData = $data;

        // Save snapshot only with lf line endings.
        return (string) preg_replace('/\r\n/', "\n", $stringData);
    }

    public function extension(): string
    {
        return 'txt';
    }

    /**
     * @inheritDoc
     */
    public function match($expected, $actual): void
    {
        /** @var string $stringExpected */
        $stringExpected = $expected;

        // Make sure the expected string has lf line endings, so we can
        // compare accurately.
        $expected = (string) preg_replace('/\r\n/', "\n", $stringExpected);

        Assert::assertEquals($expected, $this->serialize($actual));
    }
}
