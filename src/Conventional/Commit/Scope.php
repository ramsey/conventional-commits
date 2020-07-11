<?php

/**
 * This file is part of ramsey/captainhook-conventional
 *
 * ramsey/captainhook-conventional is open source software: you can distribute
 * it and/or modify it under the terms of the MIT License (the "License"). You
 * may not use this file except in compliance with the License.
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

namespace Ramsey\Conventional\Commit;

/**
 * A Conventional Commits scope
 *
 * From the Conventional Commits 1.0.0 specification:
 *
 * > 4. A scope MAY be provided after a type. A scope MUST consist of a noun
 * > describing a section of the codebase surrounded by parenthesis, e.g.,
 * > `fix(parser):`.
 *
 * @link https://www.conventionalcommits.org/en/v1.0.0/#specification Conventional Commits
 */
class Scope extends Noun
{
}
