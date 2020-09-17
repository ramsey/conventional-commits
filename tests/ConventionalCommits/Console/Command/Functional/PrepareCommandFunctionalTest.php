<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Command\Functional;

use Ramsey\Dev\Tools\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

use function realpath;

use const PHP_EOL;

class PrepareCommandFunctionalTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @param string[] $input
     *
     * @dataProvider provideInput
     */
    public function testPrepareCommand(array $input, string $configFile): void
    {
        $cli = realpath(__DIR__ . '/../../../../../bin/conventional-commits');

        $inputStream = new InputStream();
        $process = new Process(['php', $cli, 'prepare', '--config', $configFile]);
        $process->setInput($inputStream);

        $process->start();

        foreach ($input as $inputLine) {
            $inputStream->write($inputLine . PHP_EOL);
        }

        $inputStream->close();
        $process->wait();

        $this->assertMatchesTextSnapshot($process->getOutput());
    }

    /**
     * @return array<array{input: string[], configFile: string}>
     */
    public function provideInput(): array
    {
        return [
            [
                'input' => [
                    'feat',
                    '',
                    'this is a test',
                    '',
                    '',
                    '',
                ],
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/default.json'),
            ],
            [
                'input' => [
                    'invalid type',
                    'feat',
                    '',
                    'this is a test',
                    '',
                    '',
                    '',
                ],
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/default.json'),
            ],
            [
                'input' => [
                    'fix',
                    'config',
                    'use the correct config value',
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id lorem urna. Vivamus mollis arcu '
                        . 'id maximus mattis. Nunc cursus dolor ac eros facilisis sodales. Nulla facilisi. Vestibulum '
                        . 'semper nec nibh sit amet maximus. Phasellus nec arcu nulla. Mauris neque nunc, accumsan ut '
                        . 'maximus eget, pellentesque porta ante. Pellentesque lacinia bibendum ipsum, sit amet '
                        . 'faucibus est tincidunt et. Curabitur tempus ultrices commodo.',
                    'yes',
                    'Lorem ipsum dolor sit amet',
                    'yes',
                    'fix',
                    '4001',
                    're',
                    '5001',
                    '',
                    'yes',
                    'See-also',
                    'https://example.com/foo',
                    '',
                ],
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/default.json'),
            ],
            [
                'input' => [
                    'qux',
                    'foo',
                    '',
                    'quux',
                    'BAZ',
                    'baz',
                    'a short description',
                    'A short description',
                    'A short description.',
                    '',
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam id lorem urna. Vivamus mollis arcu '
                        . 'id maximus mattis. Nunc cursus dolor ac eros facilisis sodales. Nulla facilisi. Vestibulum '
                        . 'semper nec nibh sit amet maximus. Phasellus nec arcu nulla. Mauris neque nunc, accumsan ut '
                        . 'maximus eget, pellentesque porta ante. Pellentesque lacinia bibendum ipsum, sit amet '
                        . 'faucibus est tincidunt et. Curabitur tempus ultrices commodo.',
                    'no',
                    'no',
                    '',
                    'Signed-off-by',
                    '',
                    'Jane Doe <jane@example.com>',
                    'foo bar baz',
                    '',
                    'See-also',
                    'Foo: Bar',
                    'https://example.com/foo',
                    '',
                ],
                'configFile' => (string) realpath(__DIR__ . '/../../../../configs/config-03.json'),
            ],
        ];
    }
}
