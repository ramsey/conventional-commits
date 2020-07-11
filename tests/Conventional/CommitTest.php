<?php

declare(strict_types=1);

namespace Ramsey\Test\Conventional;

use Ramsey\Conventional\Commit;
use Ramsey\Conventional\Commit\Body;
use Ramsey\Conventional\Commit\Description;
use Ramsey\Conventional\Commit\Footer;
use Ramsey\Conventional\Commit\Scope;
use Ramsey\Conventional\Commit\Type;
use Ramsey\Test\Conventional\Commit\BodyTest;
use Ramsey\Test\RamseyTestCase;

use const PHP_EOL;

class CommitTest extends RamseyTestCase
{
    public function testBasicCommit(): void
    {
        $expectedMessage = 'feat: implement awesome thing' . PHP_EOL;

        $type = new Type('feat');
        $description = new Description('implement awesome thing');
        $commit = new Commit($type, $description);

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

        $commit = new Commit($type, $description);
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

        $commit = new Commit($type, $description);
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

        $commit = new Commit($type, $description);
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

        $commit = new Commit($type, $description);
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

        $commit = new Commit($type, $description, true);

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

        $commit = new Commit(new Type('feat'), new Description('implement awesome thing'));
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
}
