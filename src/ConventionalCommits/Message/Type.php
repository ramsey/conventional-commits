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

namespace Ramsey\ConventionalCommits\Message;

/**
 * A Conventional Commits type
 *
 * From the Conventional Commits 1.0.0 specification:
 *
 * > 1. Commits MUST be prefixed with a type, which consists of a noun, `feat`,
 * > `fix`, etc., followed by the OPTIONAL scope, OPTIONAL `!`, and REQUIRED
 * > terminal colon and space.
 * >
 * > 2. The type `feat` MUST be used when a commit adds a new feature to your
 * > application or library.
 * >
 * > 3. The type `fix` MUST be used when a commit represents a bug fix for your
 * > application.
 * >
 * > 14. Types other than `feat` and `fix` MAY be used in your commit messages,
 * > e.g., *docs: updated ref docs*.
 *
 * @link https://www.conventionalcommits.org/en/v1.0.0/#specification Conventional Commits
 */
class Type extends Noun
{
}
