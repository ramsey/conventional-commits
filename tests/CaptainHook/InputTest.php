<?php

declare(strict_types=1);

namespace Ramsey\Test\CaptainHook;

use CaptainHook\App\Console\IO;
use InvalidArgumentException;
use Mockery\MockInterface;
use Ramsey\CaptainHook\Input;
use Ramsey\Test\TestCase;

class InputTest extends TestCase
{
    private Input $input;

    public function setUp(): void
    {
        /** @var IO & MockInterface $captainHookIo */
        $captainHookIo = $this->mockery(IO::class, [
            'isInteractive' => true,
            'getArguments' => [
                'file' => 'aGitCommitLogMessageFile',
                'mode' => 'aCommitMode',
                'hash' => 'aCommitHash',
            ],
        ]);

        $this->input = new Input($captainHookIo);
    }

    public function testIsInteractive(): void
    {
        $this->assertTrue($this->input->isInteractive());
    }

    public function testHasArgument(): void
    {
        $this->assertTrue($this->input->hasArgument('file'));
        $this->assertFalse($this->input->hasArgument('foobar'));
    }

    public function testGetArgument(): void
    {
        $this->assertSame('aCommitMode', $this->input->getArgument('mode'));
    }

    public function testGetArgumentThrowsExceptionForUnknownArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "foobar" argument does not exist.');

        $this->input->getArgument('foobar');
    }

    public function testGetArguments(): void
    {
        $this->assertSame(
            [
                'file' => 'aGitCommitLogMessageFile',
                'mode' => 'aCommitMode',
                'hash' => 'aCommitHash',
            ],
            $this->input->getArguments(),
        );
    }

    public function testGetFirstArgument(): void
    {
        $this->assertSame('aGitCommitLogMessageFile', $this->input->getFirstArgument());
    }

    public function testGetFirstArgumentReturnsNullWhenThereAreNoArguments(): void
    {
        /** @var IO & MockInterface $captainHookIo */
        $captainHookIo = $this->mockery(IO::class, [
            'isInteractive' => true,
            'getArguments' => [],
        ]);

        $input = new Input($captainHookIo);

        $this->assertNull($input->getFirstArgument());
    }

    public function testGetOptionThrowsExceptionForUnknownOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "foo" option does not exist.');

        $this->assertNull($this->input->getOption('foo'));
    }
}
