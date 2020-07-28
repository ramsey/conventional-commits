<?php

declare(strict_types=1);

namespace Ramsey\Test\CaptainHook;

use CaptainHook\App\Console\IO;
use Mockery\MockInterface;
use Ramsey\CaptainHook\Output;
use Ramsey\Test\RamseyTestCase;
use Symfony\Component\Console\Output\OutputInterface;

class OutputTest extends RamseyTestCase
{
    public function testDebugVerbosity(): void
    {
        /** @var IO | MockInterface $captainHookIO */
        $captainHookIO = $this->mockery(IO::class, [
            'isDebug' => true,
        ]);

        $output = new Output($captainHookIO);

        $this->assertSame(OutputInterface::VERBOSITY_DEBUG, $output->getVerbosity());
    }

    public function testVeryVerboseVerbosity(): void
    {
        /** @var IO | MockInterface $captainHookIO */
        $captainHookIO = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => true,
        ]);

        $output = new Output($captainHookIO);

        $this->assertSame(OutputInterface::VERBOSITY_VERY_VERBOSE, $output->getVerbosity());
    }

    public function testVerboseVerbosity(): void
    {
        /** @var IO | MockInterface $captainHookIO */
        $captainHookIO = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => true,
        ]);

        $output = new Output($captainHookIO);

        $this->assertSame(OutputInterface::VERBOSITY_VERBOSE, $output->getVerbosity());
    }

    public function testNormalVerbosity(): void
    {
        /** @var IO | MockInterface $captainHookIO */
        $captainHookIO = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        $output = new Output($captainHookIO);

        $this->assertSame(OutputInterface::VERBOSITY_NORMAL, $output->getVerbosity());
    }

    public function testWrite(): void
    {
        /** @var IO | MockInterface $captainHookIO */
        $captainHookIO = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        $captainHookIO
            ->expects()
            ->write('this is a test', true);

        $output = new Output($captainHookIO);

        $output->write('this is a test', true);
    }
}
