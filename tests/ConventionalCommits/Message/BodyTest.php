<?php

declare(strict_types=1);

namespace Ramsey\Test\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\Test\RamseyTestCase;

use const PHP_EOL;

class BodyTest extends RamseyTestCase
{
    public function testBody(): void
    {
        $body = new Body($this->getRawBodyForTest());

        $this->assertSame($this->getExpectedBody(), $body->toString());
        $this->assertSame($this->getExpectedBody(), (string) $body);
    }

    public function getExpectedBody(): string
    {
        $body = $this->getRawBodyForTest();

        // Replace line feeds with PHP_EOL, in case tests run on platforms with
        // different line endings.
        return str_replace("\n", PHP_EOL, $body);
    }

    /**
     * phpcs:disable
     */
    public function getRawBodyForTest(): string
    {
        return <<<'EOD'
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce pharetra quis massa ut elementum. Pellentesque dapibus ultricies pharetra. In in finibus massa. Donec cursus nec ligula nec eleifend. Ut eleifend ut nisl sit amet laoreet. Mauris tristique nibh urna, vestibulum dictum ante tincidunt a. Curabitur ultricies dignissim neque eget rutrum. Maecenas nulla diam, dictum et pellentesque tristique, vestibulum ut dolor. Nam ac lobortis turpis. Nulla faucibus eu ex tempus tempus. Mauris tincidunt pellentesque velit quis ullamcorper. Nam massa mauris, porta vel varius id, sollicitudin vitae dolor. Aliquam at mi sem. Sed rhoncus urna sapien, vel vehicula urna sagittis eget.

            ``` php
            function loremIpsum(string $ipsum): string
            {
                return 'a string';
            }
            ```

            Morbi tincidunt pulvinar sem. https://example.com/this-is-a-really-long-url-that-should-not-break-across-lines-when-wrapping In posuere eros et venenatis mattis. Nullam tincidunt vehicula ullamcorper. Vivamus posuere eros eget condimentum posuere. Sed vel magna quis neque facilisis aliquet eu sed nisi. Nam nec arcu mi. Fusce laoreet mi sed mi egestas, sed dignissim nibh ultricies. Maecenas viverra tellus eros, nec ullamcorper magna cursus quis. In a nulla et nisi sagittis vehicula.
            EOD;
    }
}
