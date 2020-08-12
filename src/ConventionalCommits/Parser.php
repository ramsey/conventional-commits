<?php

/**
 * This file is part of ramsey/conventional-commits
 *
 * ramsey/conventional-commits is open source software: you can distribute it
 * and/or modify it under the terms of the MIT License (the "License"). You may
 * not use this file except in compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\ConventionalCommits;

use Ramsey\ConventionalCommits\Exception\InvalidCommitMessage;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;

use function count;
use function preg_match;
use function preg_match_all;
use function trim;

/**
 * Validates and parses a Conventional Commits message
 */
class Parser
{
    private const COMMIT_PATTERN = "/^(?(DEFINE)(?'noun'[A-Z0-9][\w-]+)"
        . "(?'tokenPrefix'(?:BREAKING CHANGE|(?P>noun))\ *(?:\:\ |\ \#(?=\w))))"
        . "(?'type'(?P>noun))"
        . "(?:\((?'scope'(?P>noun))\))?(?'bc'!)?: "
        . "(?'desc'[[:print:]]+)"
        . "(?:(?:\n{2}|\r{2}|(?:\r\n){2})"
        . "(?'body'.*?(?=(?P>tokenPrefix)|\$))?"
        . "(?:(?=(?P>tokenPrefix))(?'footer'.*))?)?\$/ius";

    private const FOOTER_PATTERN = "/^(?(DEFINE)(?'noun'[A-Z0-9][\w-]+)"
        . "(?'tokenName'BREAKING CHANGE|(?P>noun))(?'tokenSeparator'\:\ |\ \#(?=\w))"
        . "(?'tokenPrefix'(?P>tokenName)\ *(?P>tokenSeparator)))"
        . "(?'footer'(?'token'(?P>tokenName))\ *(?'separator'(?P>tokenSeparator))\ *"
        . "(?'value'(?:.*?)(?=(?P>tokenPrefix))|(?:.*)))/iusm";

    /**
     * Parses a commit message, returning a Message instance or throwing an
     * exception on failure
     */
    public function parse(string $commitMessage): Message
    {
        $commitMessage = trim($commitMessage);

        if (!preg_match(self::COMMIT_PATTERN, $commitMessage, $matches)) {
            throw new InvalidCommitMessage(
                'Could not find a valid Conventional Commits message',
            );
        }

        $type = new Type($matches['type']);
        $description = new Description($matches['desc']);
        $hasBreakingChanges = trim($matches['bc'] ?? '') === '!';

        $commit = new Message($type, $description, $hasBreakingChanges);

        if (trim($matches['scope'] ?? '') !== '') {
            $commit->setScope(new Scope($matches['scope']));
        }

        if (trim($matches['body'] ?? '') !== '') {
            $commit->setBody(new Body($matches['body']));
        }

        foreach ($this->parseFooter($matches['footer'] ?? '') as $footer) {
            $commit->addFooter($footer);
        }

        return $commit;
    }

    /**
     * @return array<Footer>
     */
    private function parseFooter(string $footer): array
    {
        if (!preg_match_all(self::FOOTER_PATTERN, $footer, $matches)) {
            return [];
        }

        /**
         * Psalm needs this because there's no way to define the array structure
         * above the preg_match_all() statement where $matches is instantiated.
         *
         * @var array{
         *     token: list<string>,
         *     separator: list<string>,
         *     value: list<string>
         * } $footerParams
         */
        $footerParams = $matches;

        $footers = [];

        for ($i = 0; $i < count($footerParams['token']); $i++) {
            $footers[] = new Footer(
                $footerParams['token'][$i],
                $footerParams['value'][$i],
                $footerParams['separator'][$i],
            );
        }

        return $footers;
    }
}
