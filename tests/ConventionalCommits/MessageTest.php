<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits;

use Ramsey\ConventionalCommits\Configuration\DefaultConfiguration;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;
use Ramsey\ConventionalCommits\Parser;
use Ramsey\Test\ConventionalCommits\Message\BodyTest;
use Ramsey\Test\SnapshotsTool;
use Ramsey\Test\TestCase;
use Ramsey\Test\WindowsSafeTextDriver;

use function file_get_contents;
use function preg_replace;
use function realpath;

use const PHP_EOL;

class MessageTest extends TestCase
{
    use SnapshotsTool;

    public function testBasicCommit(): void
    {
        $expectedMessage = 'feat: implement awesome thing' . PHP_EOL;

        $type = new Type('feat');
        $description = new Description('implement awesome thing');
        $commit = new Message($type, $description);

        $this->assertSame($type, $commit->getType());
        $this->assertSame($description, $commit->getDescription());
        $this->assertNull($commit->getScope());
        $this->assertNull($commit->getBody());
        $this->assertSame([], $commit->getFooters());
        $this->assertFalse($commit->hasBreakingChanges());
        $this->assertSame($expectedMessage, $commit->toString());
        $this->assertSame($expectedMessage, (string) $commit);
    }

    public function testScope(): void
    {
        $expectedMessage = 'feat(my-scope): implement awesome thing' . PHP_EOL;

        $type = new Type('feat');
        $description = new Description('implement awesome thing');
        $scope = new Scope('my-scope');

        $commit = new Message($type, $description);
        $commit->setScope($scope);

        $this->assertSame($scope, $commit->getScope());
        $this->assertSame($expectedMessage, $commit->toString());
    }

    public function testBody(): void
    {
        $bodyTest = new BodyTest();

        $expectedMessage = 'feat: implement awesome thing' . PHP_EOL . PHP_EOL
            . $bodyTest->getExpectedBody() . PHP_EOL;

        $type = new Type('feat');
        $description = new Description('implement awesome thing');
        $body = new Body($bodyTest->getRawBodyForTest());

        $commit = new Message($type, $description);
        $commit->setBody($body);

        $this->assertSame($body, $commit->getBody());
        $this->assertSame($expectedMessage, $commit->toString());
    }

    public function testFooters(): void
    {
        $expectedMessage = 'feat: implement awesome thing' . PHP_EOL . PHP_EOL
            . 'Fix #1234' . PHP_EOL
            . 'Signed-off-by: Alice <alice@example.com>' . PHP_EOL
            . 'Acked-by: Bob <bob@example.com>' . PHP_EOL
            . 'See-also: fe3187489d69c4' . PHP_EOL;

        $type = new Type('feat');
        $description = new Description('implement awesome thing');

        $commit = new Message($type, $description);
        $commit->addFooter(new Footer('Fix', '1234', Footer::SEPARATOR_HASH));
        $commit->addFooter(new Footer('Signed-off-by', 'Alice <alice@example.com>'));
        $commit->addFooter(new Footer('Acked-by', 'Bob <bob@example.com>'));
        $commit->addFooter(new Footer('See-also', 'fe3187489d69c4'));

        $this->assertContainsOnlyInstancesOf(Footer::class, $commit->getFooters());
        $this->assertSame($expectedMessage, $commit->toString());
    }

    public function testSetBreakingChangeWithFooter(): void
    {
        $expectedMessage = 'fix!: fix a bug' . PHP_EOL . PHP_EOL
            . 'BREAKING CHANGE: this is a breaking change' . PHP_EOL
            . 'Fix #1234' . PHP_EOL;

        $type = new Type('fix');
        $description = new Description('fix a bug');

        $commit = new Message($type, $description);
        $commit->addFooter(new Footer('Breaking Change', 'this is a breaking change'));
        $commit->addFooter(new Footer('Fix', '1234', Footer::SEPARATOR_HASH));

        $this->assertContainsOnlyInstancesOf(Footer::class, $commit->getFooters());
        $this->assertTrue($commit->hasBreakingChanges());
        $this->assertSame($expectedMessage, $commit->toString());
    }

    public function testWithBreakingChange(): void
    {
        $expectedMessage = 'fix!: fix a bug' . PHP_EOL;

        $type = new Type('fix');
        $description = new Description('fix a bug');

        $commit = new Message($type, $description, true);

        $this->assertTrue($commit->hasBreakingChanges());
        $this->assertSame($expectedMessage, $commit->toString());
    }

    public function testToStringIncludesEverything(): void
    {
        $bodyTest = new BodyTest();

        $expectedMessage = 'feat(my-scope)!: implement awesome thing' . PHP_EOL . PHP_EOL
            . $bodyTest->getExpectedBody() . PHP_EOL . PHP_EOL
            . 'Fix #1234' . PHP_EOL
            . 'Signed-off-by: Alice <alice@example.com>' . PHP_EOL
            . 'Acked-by: Bob <bob@example.com>' . PHP_EOL
            . 'See-also: fe3187489d69c4' . PHP_EOL
            . 'BREAKING CHANGE: this is a breaking change' . PHP_EOL;

        $commit = new Message(new Type('feat'), new Description('implement awesome thing'));
        $commit->setScope(new Scope('my-scope'));
        $commit->setBody(new Body($bodyTest->getRawBodyForTest()));
        $commit->addFooter(new Footer('Fix', '1234', Footer::SEPARATOR_HASH));
        $commit->addFooter(new Footer('Signed-off-by', 'Alice <alice@example.com>'));
        $commit->addFooter(new Footer('Acked-by', 'Bob <bob@example.com>'));
        $commit->addFooter(new Footer('See-also', 'fe3187489d69c4'));
        $commit->addFooter(new Footer('BREAKING CHANGE', 'this is a breaking change'));

        $this->assertSame($expectedMessage, $commit->toString());
        $this->assertSame($expectedMessage, (string) $commit);
    }

    public function testParserCallsValidateOnMessageAndDoesNotThrowExceptions(): void
    {
        $config = new DefaultConfiguration([
            'typeCase' => 'kebab',
            'types' => ['feat', 'fix', 'foo', 'bar'],
            'scopeCase' => 'kebab',
            'scopeRequired' => true,
            'scopes' => ['component', 'unit', 'my-scope'],
            'descriptionCase' => 'sentence',
            'descriptionEndMark' => '.',
            'bodyRequired' => true,
            'requiredFooters' => ['Signed-off-by'],
        ]);

        $raw = (string) file_get_contents((string) realpath(__DIR__ . '/commit-messages/commit-message-01.txt'));

        $parser = new Parser($config);
        $message = $parser->parse($raw);

        $this->assertInstanceOf(Message::class, $message);
    }

    public function testThrowsInvalidValueWhenScopeIsRequiredAndMissing(): void
    {
        $config = new DefaultConfiguration([
            'scopeRequired' => true,
        ]);

        $message = new Message(new Type('foo'), new Description('a description'));
        $message->setBody(new Body('this is a body'));
        $message->setConfiguration($config);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('You must provide a scope');

        $message->validate();
    }

    public function testThrowsInvalidValueWhenBodyIsRequiredAndMissing(): void
    {
        $config = new DefaultConfiguration([
            'bodyRequired' => true,
        ]);

        $message = new Message(new Type('foo'), new Description('a description'));
        $message->setScope(new Scope('aScope'));
        $message->setConfiguration($config);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('You must provide a body');

        $message->validate();
    }

    public function testMessageWrapsBodyWhenBodyWrappingEnabled(): void
    {
        $config = new DefaultConfiguration([
            'bodyWrapWidth' => 72,
        ]);

        $raw = (string) file_get_contents((string) realpath(__DIR__ . '/commit-messages/commit-message-07.txt'));

        // Fix line endings in case running tests on Windows.
        $raw = (string) preg_replace('/(?<!\r)\n/', PHP_EOL, $raw);

        $parser = new Parser($config);
        $message = $parser->parse($raw);

        $this->assertMatchesSnapshot($message->toString(), new WindowsSafeTextDriver());
    }
}
