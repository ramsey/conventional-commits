<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Configuration;

use JsonException;
use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Configuration\Configuration;
use Ramsey\ConventionalCommits\Configuration\FinderTool;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\Test\SnapshotsTool;
use Ramsey\Test\TestCase;
use Ramsey\Test\WindowsSafeTextDriver;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function file_put_contents;
use function json_encode;
use function realpath;
use function sys_get_temp_dir;
use function unlink;

use const DIRECTORY_SEPARATOR;

class FinderToolTest extends TestCase
{
    use SnapshotsTool;

    private InputInterface & MockInterface $input;

    private object $finderTool;

    private OutputInterface & MockInterface $output;

    private Filesystem $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->input = $this->mockery(InputInterface::class);
        $this->output = $this->mockery(OutputInterface::class);
        $this->finderTool = new class () {
            use FinderTool;
        };
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param mixed[] $options
     *
     * @throws JsonException
     *
     * @dataProvider provideOptions
     */
    public function testFindConfigurationReturnsConfigurationForPassedArray(array $options): void
    {
        $this->assertMatchesSnapshot(
            json_encode(
                // @phpstan-ignore-next-line
                $this->finderTool->findConfiguration($this->input, $this->output, $options),
            ),
            new WindowsSafeTextDriver(),
        );
    }

    /**
     * @return array<array{options: mixed[]}>
     */
    public static function provideOptions(): array
    {
        return [
            [
                'options' => [
                    'config' => [
                        'typeCase' => 'ada',
                    ],
                ],
            ],
            [
                'options' => [
                    'config' => [],
                ],
            ],
            [
                'options' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/default.json'),
                ],
            ],
            [
                'options' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-01.json'),
                ],
            ],
            [
                'options' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-02.json'),
                ],
            ],
            [
                'options' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-03.json'),
                ],
            ],
            [
                // The FinderTool should use `config` from this set of options,
                // rather than the `configFile`.
                'options' => [
                    'configFile' => (string) realpath(__DIR__ . '/configs/config-03.json'),
                    'config' => [
                        'typeCase' => 'kebab',
                        'types' => ['tests', 'docs'],
                        'scopeCase' => 'train',
                        'scopeRequired' => true,
                        'scopes' => ['message', 'console'],
                        'descriptionCase' => 'title',
                        'descriptionEndMark' => '',
                        'bodyRequired' => true,
                        'bodyWrapWidth' => 80,
                        'requiredFooters' => ['Some-footer'],
                        'throwAwayProperty' => 'this should not appear in snapshot',
                    ],
                ],
            ],
        ];
    }

    public function testFindConfigurationThrowsExceptionWhenConfigFileDoesNotExist(): void
    {
        $configFile = (string) realpath(__DIR__ . '/../../configs')
            . DIRECTORY_SEPARATOR . 'config-file-not-exists.json';

        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage("Could not find config file '{$configFile}'");

        // @phpstan-ignore-next-line
        $this->finderTool->findConfiguration($this->input, $this->output, [
            'configFile' => $configFile,
        ]);
    }

    public function testFindConfigurationThrowsExceptionWhenConfigFileHasInvalidValue(): void
    {
        $configFile = (string) realpath(__DIR__ . '/../../configs/config-04.json');

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            "Expected a configuration array in {$configFile}; received string instead.",
        );

        // @phpstan-ignore-next-line
        $this->finderTool->findConfiguration($this->input, $this->output, [
            'configFile' => $configFile,
        ]);
    }

    public function testFindConfigurationThrowsExceptionWhenConfigIsInvalid(): void
    {
        $configFile = (string) realpath(__DIR__ . '/../../configs/config-05.json');

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid /types value found in configuration: The data (string) must match the type: array',
        );

        // @phpstan-ignore-next-line
        $this->finderTool->findConfiguration($this->input, $this->output, [
            'configFile' => $configFile,
        ]);
    }

    public function testFindConfigurationThrowsExceptionWhenComposerJsonDoesNotExist(): void
    {
        $composerJsonPath = 'path/to/nonexistent/composer.json';

        $finderTool = new class () {
            use FinderTool;

            public string $composerJsonPath;

            public function findComposerJson(Filesystem $filesystem): string
            {
                return $this->composerJsonPath;
            }
        };

        $finderTool->composerJsonPath = $composerJsonPath;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The file \"{$composerJsonPath}\" is not readable.");

        $finderTool->findConfiguration($this->input, $this->output);
    }

    public function testFindConfigurationThrowsExceptionWhenComposerHasInvalidValue(): void
    {
        $composerJsonPath = $this->fileSystem->tempnam(sys_get_temp_dir(), 'cc_', '.json');
        file_put_contents($composerJsonPath, json_encode([
            'extra' => [
                'ramsey/conventional-commits' => [
                    'config' => 'invalid value',
                ],
            ],
        ]));

        $finderTool = new class () {
            use FinderTool;

            public string $composerJsonPath;

            public function findComposerJson(Filesystem $filesystem): string
            {
                return $this->composerJsonPath;
            }
        };

        $finderTool->composerJsonPath = $composerJsonPath;

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Expected a configuration array in composer.json extra.ramsey/conventional-commits.config; '
            . 'received string instead.',
        );

        $finderTool->findConfiguration($this->input, $this->output);

        @unlink($composerJsonPath);
    }

    public function testFindConfigurationReturnsConfigurationUsingComposerConfig(): void
    {
        $composerJsonPath = $this->fileSystem->tempnam(sys_get_temp_dir(), 'cc_', '.json');
        file_put_contents($composerJsonPath, json_encode([
            'extra' => [
                'ramsey/conventional-commits' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-03.json'),
                    'config' => [
                        'typeCase' => 'pascal',
                    ],
                ],
            ],
        ]));

        $finderTool = new class () {
            use FinderTool;

            public string $composerJsonPath;

            public function findComposerJson(Filesystem $filesystem): string
            {
                return $this->composerJsonPath;
            }
        };

        $finderTool->composerJsonPath = $composerJsonPath;

        /** @var Configuration $configuration */
        $configuration = $finderTool->findConfiguration($this->input, $this->output);

        $this->assertMatchesSnapshot(json_encode($configuration), new WindowsSafeTextDriver());

        @unlink($composerJsonPath);
    }

    public function testFindConfigurationReturnsConfigurationUsingComposerConfigFile(): void
    {
        $composerJsonPath = $this->fileSystem->tempnam(sys_get_temp_dir(), 'cc_', '.json');
        file_put_contents($composerJsonPath, json_encode([
            'extra' => [
                'ramsey/conventional-commits' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-03.json'),
                ],
            ],
        ]));

        $finderTool = new class () {
            use FinderTool;

            public string $composerJsonPath;

            public function findComposerJson(Filesystem $filesystem): string
            {
                return $this->composerJsonPath;
            }
        };

        $finderTool->composerJsonPath = $composerJsonPath;

        /** @var Configuration $configuration */
        $configuration = $finderTool->findConfiguration($this->input, $this->output);

        $this->assertMatchesSnapshot(json_encode($configuration), new WindowsSafeTextDriver());

        @unlink($composerJsonPath);
    }

    public function testFindConfigurationReturnsDefaultConfigurationWhenComposerHasNone(): void
    {
        $composerJsonPath = $this->fileSystem->tempnam(sys_get_temp_dir(), 'cc_', '.json');
        file_put_contents($composerJsonPath, json_encode([
            'extra' => [],
        ]));

        $finderTool = new class () {
            use FinderTool;

            public string $composerJsonPath;

            public function findComposerJson(Filesystem $filesystem): string
            {
                return $this->composerJsonPath;
            }
        };

        $finderTool->composerJsonPath = $composerJsonPath;

        /** @var Configuration $configuration */
        $configuration = $finderTool->findConfiguration($this->input, $this->output);

        $this->assertMatchesSnapshot(json_encode($configuration), new WindowsSafeTextDriver());

        @unlink($composerJsonPath);
    }
}
