<?php

declare(strict_types=1);

namespace Ramsey\Test;

use ReflectionClass;
use Spatie\Snapshots\MatchesSnapshots;

use function preg_replace;
use function str_starts_with;

use const PHP_OS;

trait SnapshotsTool
{
    use MatchesSnapshots;

    protected function getSnapshotId(): string
    {
        $suffix = '';
        if (str_starts_with(PHP_OS, 'WIN')) {
            $suffix = '__WIN';
        }

        $snapshotId = (new ReflectionClass($this))->getShortName()
            . '__'
            . $this->nameWithDataSet()
            . '__'
            . $this->snapshotIncrementor
            . $suffix;

        return (string) preg_replace('/[^0-9a-z]/i', '_', $snapshotId);
    }
}
