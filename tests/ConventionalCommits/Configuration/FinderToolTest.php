<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Configuration;

use Composer\Composer;
use JsonException;
use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Configuration\Configuration;
use Ramsey\ConventionalCommits\Configuration\FinderTool;
use Ramsey\ConventionalCommits\Exception\ComposerNotFound;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\Dev\Tools\TestCase;
use Ramsey\Test\SnapshotsTool;
use Ramsey\Test\WindowsSafeTextDriver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function json_encode;
use function realpath;

use const DIRECTORY_SEPARATOR;

class FinderToolTest extends TestCase
{
    use SnapshotsTool;

    /**
     * @var InputInterface & MockInterface
     */
    private $input;

    private object $finderTool;

    /**
     * @var OutputInterface & MockInterface
     */
    private $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->input = $this->mockery(InputInterface::class);
        $this->output = $this->mockery(OutputInterface::class);
        $this->finderTool = new class () {
            use FinderTool;
        };
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
    public function provideOptions(): array
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
            'Invalid types value found in configuration; expected array, used string.',
        );

        // @phpstan-ignore-next-line
        $this->finderTool->findConfiguration($this->input, $this->output, [
            'configFile' => $configFile,
        ]);
    }

    public function testGetComposerFindsComposerJsonForCurrentProject(): void
    {
        $this->output->allows()->getVerbosity()->andReturn(OutputInterface::VERBOSITY_QUIET);
        $filesystem = new Filesystem();

        // @phpstan-ignore-next-line
        $composer = $this->finderTool->getComposer($this->input, $this->output, $filesystem);

        $this->assertInstanceOf(Composer::class, $composer);
        $this->assertSame('ramsey/conventional-commits', $composer->getPackage()->getName());
    }

    public function testGetComposerThrowsExceptionWhenAutoloaderDoesNotExist(): void
    {
        $this->output->allows()->getVerbosity()->andReturn(OutputInterface::VERBOSITY_QUIET);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('exists')->twice()->andReturnFalse();

        $this->expectException(ComposerNotFound::class);
        $this->expectExceptionMessage(
            'Could not find the autoloader. Did you run composer install or composer update?',
        );

        // @phpstan-ignore-next-line
        $this->finderTool->getComposer($this->input, $this->output, $filesystem);
    }

    public function testGetComposerThrowsExceptionWhenComposerJsonDoesNotExist(): void
    {
        $this->output->allows()->getVerbosity()->andReturn(OutputInterface::VERBOSITY_QUIET);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('exists')->andReturn(false, true, false);

        $this->expectException(ComposerNotFound::class);
        $this->expectExceptionMessage('Could not find composer.json.');

        // @phpstan-ignore-next-line
        $this->finderTool->getComposer($this->input, $this->output, $filesystem);
    }

    public function testFindConfigurationThrowsExceptionWhenComposerHasInvalidValue(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'ramsey/conventional-commits' => [
                    'config' => 'invalid value',
                ],
            ],
        ]);

        $finderTool = new class () {
            use FinderTool;

            public Composer $composer;

            public function getComposer(
                InputInterface $input,
                OutputInterface $output,
                Filesystem $filesystem
            ): Composer {
                return $this->composer;
            }
        };

        $finderTool->composer = $composer;

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Expected a configuration array in composer.json extra.ramsey/conventional-commits.config; '
            . 'received string instead.',
        );

        $finderTool->findConfiguration($this->input, $this->output);
    }

    public function testFindConfigurationReturnsConfigurationUsingComposerConfig(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'ramsey/conventional-commits' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-03.json'),
                    'config' => [
                        'typeCase' => 'pascal',
                    ],
                ],
            ],
        ]);

        $finderTool = new class () {
            use FinderTool;

            public Composer $composer;

            public function getComposer(
                InputInterface $input,
                OutputInterface $output,
                Filesystem $filesystem
            ): Composer {
                return $this->composer;
            }
        };

        $finderTool->composer = $composer;

        /** @var Configuration $configuration */
        $configuration = $finderTool->findConfiguration($this->input, $this->output);

        $this->assertMatchesSnapshot(json_encode($configuration), new WindowsSafeTextDriver());
    }

    public function testFindConfigurationReturnsConfigurationUsingComposerConfigFile(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [
                'ramsey/conventional-commits' => [
                    'configFile' => (string) realpath(__DIR__ . '/../../configs/config-03.json'),
                ],
            ],
        ]);

        $finderTool = new class () {
            use FinderTool;

            public Composer $composer;

            public function getComposer(
                InputInterface $input,
                OutputInterface $output,
                Filesystem $filesystem
            ): Composer {
                return $this->composer;
            }
        };

        $finderTool->composer = $composer;

        /** @var Configuration $configuration */
        $configuration = $finderTool->findConfiguration($this->input, $this->output);

        $this->assertMatchesSnapshot(json_encode($configuration), new WindowsSafeTextDriver());
    }

    public function testFindConfigurationReturnsDefaultConfigurationWhenComposerHasNone(): void
    {
        /** @var Composer & MockInterface $composer */
        $composer = $this->mockery(Composer::class, [
            'getPackage->getExtra' => [],
        ]);

        $finderTool = new class () {
            use FinderTool;

            public Composer $composer;

            public function getComposer(
                InputInterface $input,
                OutputInterface $output,
                Filesystem $filesystem
            ): Composer {
                return $this->composer;
            }
        };

        $finderTool->composer = $composer;

        /** @var Configuration $configuration */
        $configuration = $finderTool->findConfiguration($this->input, $this->output);

        $this->assertMatchesSnapshot(json_encode($configuration), new WindowsSafeTextDriver());
    }
}
