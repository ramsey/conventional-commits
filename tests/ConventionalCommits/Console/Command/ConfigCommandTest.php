<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Console\Command;

use Mockery\MockInterface;
use Ramsey\ConventionalCommits\Console\Command\ConfigCommand;
use Ramsey\ConventionalCommits\Console\SymfonyStyleFactory;
use Ramsey\Dev\Tools\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_get_contents;
use function trim;

class ConfigCommandTest extends TestCase
{
    public function testCommandName(): void
    {
        $command = new ConfigCommand();

        $this->assertSame('config', $command->getName());
    }

    public function testRunDoesNothing(): void
    {
        /** @var SymfonyStyle & MockInterface $style */
        $style = $this->mockery(SymfonyStyle::class);
        $style->shouldNotReceive('writeln');

        /** @var SymfonyStyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(SymfonyStyleFactory::class, [
            'factory' => $style,
        ]);

        $configFile = __DIR__ . '/../../../configs/default.json';
        $input = new StringInput("--config {$configFile}");
        $output = new NullOutput();

        $command = new ConfigCommand($styleFactory);

        $this->assertSame(Command::SUCCESS, $command->run($input, $output));
    }

    public function testRunWritesConfigToConsoleWithDump(): void
    {
        $configFile = __DIR__ . '/../../../configs/default.json';
        $configFileContents = trim((string) file_get_contents($configFile));

        /** @var SymfonyStyle & MockInterface $style */
        $style = $this->mockery(SymfonyStyle::class);
        $style
            ->expects()
            ->writeln($configFileContents);

        /** @var SymfonyStyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(SymfonyStyleFactory::class, [
            'factory' => $style,
        ]);

        $input = new StringInput("--config {$configFile} --dump");
        $output = new NullOutput();

        $command = new ConfigCommand($styleFactory);

        $this->assertSame(Command::SUCCESS, $command->run($input, $output));
    }
}
