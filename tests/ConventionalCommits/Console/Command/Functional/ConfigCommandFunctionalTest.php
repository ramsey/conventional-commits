<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Command\Functional;

use Ramsey\Dev\Tools\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Process\Process;

use function realpath;

class ConfigCommandFunctionalTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @dataProvider provideConfigs
     */
    public function testConfigCommandDumpWithVariousConfigurations(string $configFile): void
    {
        $cli = realpath(__DIR__ . '/../../../../../bin/conventional-commits');

        $process = new Process(['php', $cli, 'config', '--config', $configFile, '--dump']);
        $process->run();

        $this->assertMatchesTextSnapshot($process->getOutput());
    }

    /**
     * @return array<array{configFile: string}>
     */
    public function provideConfigs(): array
    {
        return [
            [
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/default.json'),
            ],
            [
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/config-01.json'),
            ],
            [
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/config-02.json'),
            ],
            [
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/config-03.json'),
            ],
        ];
    }
}
