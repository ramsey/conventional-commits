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

namespace Ramsey\ConventionalCommits\Configuration;

use JsonSerializable;
use Ramsey\ConventionalCommits\Converter\LetterCaseConverter;
use Ramsey\ConventionalCommits\Validator\MessageValidator;

/**
 * A configuration provides additional rules on top of the Conventional
 * Commits specification
 */
interface Configuration extends JsonSerializable
{
    /**
     * Returns the configuration as an array
     *
     * @return array{
     *     typeCase?: string|null,
     *     types?: string[],
     *     scopeCase?: string|null,
     *     scopeRequired?: bool,
     *     scopes?: string[],
     *     descriptionCase?: string|null,
     *     descriptionEndMark?: string|null,
     *     bodyRequired?: bool,
     *     bodyWrapWidth?: int|null,
     *     requiredFooters?: string[],
     * }
     */
    public function toArray(): array;

    /**
     * Returns the configured case (i.e., uppercase, lowercase, etc.) for the type
     *
     * By default, this returns `null`, which means any case is acceptable.
     */
    public function getTypeCase(): ?string;

    /**
     * Returns the configured types to accept as valid
     *
     * @return string[]
     */
    public function getTypes(): array;

    /**
     * Returns the configured case (i.e., uppercase, lowercase, etc.) for the scope
     *
     * By default, this returns `null`, which means any case is acceptable.
     */
    public function getScopeCase(): ?string;

    /**
     * Returns true if the scope is required, otherwise false (the default)
     */
    public function isScopeRequired(): bool;

    /**
     * Returns the configured scopes to accept as valid
     *
     * By default, this returns an empty array, which means any scope is valid.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * Returns the configured case (i.e., uppercase, lowercase, etc.) for the
     * description
     *
     * By default, this returns `null`, which means any case is acceptable.
     */
    public function getDescriptionCase(): ?string;

    /**
     * Returns the configured end mark (ending punctuation) for the description
     *
     * If configured, the description must end with this punctuation mark. If
     * the value is an empty string (i.e., `''`), then the description must not
     * end with any punctuation mark.
     *
     * By default, this returns `null`, which means any end mark (or no end
     * mark) is valid.
     */
    public function getDescriptionEndMark(): ?string;

    /**
     * Returns true if the body is required, otherwise false (the default)
     */
    public function isBodyRequired(): bool;

    /**
     * Returns the configured wrap width for the body
     *
     * This library automatically wraps the body to this width.
     *
     * By default, this returns `null`, which means the body will not receive
     * automatic wrapping.
     */
    public function getBodyWrapWidth(): ?int;

    /**
     * Returns an array of required footer tokens
     *
     * By default, this returns an empty array, which means there are no
     * required footers.
     *
     * @return string[]
     */
    public function getRequiredFooters(): array;

    /**
     * Returns the letter case converter configured for use with the given $case
     */
    public function getLetterCaseConverter(?string $case): LetterCaseConverter;

    /**
     * Returns the message validator to use with this configuration
     */
    public function getMessageValidator(): MessageValidator;
}
