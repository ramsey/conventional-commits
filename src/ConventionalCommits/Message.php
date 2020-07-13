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

use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;
use Stringable;

use const PHP_EOL;

/**
 * A Conventional Commits commit message
 *
 * @link https://www.conventionalcommits.org/en/v1.0.0/#specification Conventional Commits
 */
class Message implements Stringable
{
    private Type $type;
    private ?Scope $scope = null;
    private Description $description;
    private ?Body $body = null;
    private bool $hasBreakingChanges;

    /**
     * @var Footer[]
     */
    private array $footers = [];

    public function __construct(
        Type $type,
        Description $description,
        bool $hasBreakingChanges = false
    ) {
        $this->type = $type;
        $this->description = $description;
        $this->hasBreakingChanges = $hasBreakingChanges;
    }

    public function setScope(Scope $scope): void
    {
        $this->scope = $scope;
    }

    public function getScope(): ?Scope
    {
        return $this->scope;
    }

    public function setBody(Body $body): void
    {
        $this->body = $body;
    }

    public function getBody(): ?Body
    {
        return $this->body;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function addFooter(Footer $footer): void
    {
        $this->footers[] = $footer;

        if ($footer->getToken() === Footer::TOKEN_BREAKING_CHANGE) {
            $this->hasBreakingChanges = true;
        }
    }

    /**
     * @return array<Footer>
     */
    public function getFooters(): array
    {
        return $this->footers;
    }

    public function hasBreakingChanges(): bool
    {
        return $this->hasBreakingChanges;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $message = $this->type->toString();

        if ($this->scope !== null) {
            $message .= '(' . $this->scope->toString() . ')';
        }

        if ($this->hasBreakingChanges) {
            $message .= '!';
        }

        $message .= ': ' . $this->description->toString();

        if ($this->body !== null) {
            $message .= PHP_EOL . PHP_EOL . $this->body->toString();
        }

        if (count($this->footers) > 0) {
            $message .= PHP_EOL;
        }

        foreach ($this->footers as $footer) {
            $message .= PHP_EOL . $footer->toString();
        }

        $message .= PHP_EOL;

        return $message;
    }
}
