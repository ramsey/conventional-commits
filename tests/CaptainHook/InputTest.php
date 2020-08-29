<?php

declare(strict_types=1);

namespace Ramsey\Test\CaptainHook;

use CaptainHook\App\Console\IO;
use InvalidArgumentException;
use Mockery\MockInterface;
use Ramsey\CaptainHook\Input;
use Ramsey\Dev\Tools\TestCase;
use RuntimeException;
use Symfony\Component\Console\Input\InputDefinition;

class InputTest extends TestCase
{
    private Input $input;

    public function setUp(): void
    {
        /** @var IO & MockInterface $captainHookIo  */
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
        $this->expectExceptionMessage("Argument 'foobar' does not exist");

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
        /** @var IO & MockInterface $captainHookIo  */
        $captainHookIo = $this->mockery(IO::class, [
            'isInteractive' => true,
            'getArguments' => [],
        ]);

        $input = new Input($captainHookIo);

        $this->assertNull($input->getFirstArgument());
    }

    public function testAssertBindDoesNothing(): void
    {
        $definition = new InputDefinition();

        $this->input->bind($definition);
    }

    public function testAssertValidateDoesNothing(): void
    {
        $this->input->validate();
    }

    /**
     * @param mixed[] $params
     *
     * @dataProvider provideUnsupportedMethods
     */
    public function testExceptionThrownForUnsupportedMethod(string $methodName, array $params): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            "{$methodName} is not supported in this implementation",
        );

        $this->input->{$methodName}(...$params);
    }

    /**
     * @return array<array{methodName: string, params: mixed[]}>
     */
    public function provideUnsupportedMethods(): array
    {
        return [
            ['methodName' => 'hasParameterOption', 'params' => ['foo', true]],
            ['methodName' => 'getParameterOption', 'params' => ['foo', true, true]],
            ['methodName' => 'setArgument', 'params' => ['foo', 'bar']],
            ['methodName' => 'getOptions', 'params' => []],
            ['methodName' => 'getOption', 'params' => ['foo']],
            ['methodName' => 'setOption', 'params' => ['foo', 'bar']],
            ['methodName' => 'hasOption', 'params' => ['foo']],
            ['methodName' => 'setInteractive', 'params' => [true]],
        ];
    }
}
