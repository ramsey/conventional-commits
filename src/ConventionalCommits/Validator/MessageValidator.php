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

namespace Ramsey\ConventionalCommits\Validator;

use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;

/**
 * A message validator provides validation for all the parts of a commit message
 */
interface MessageValidator extends Validator
{
    /**
     * Returns true if type is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateType(Type $type): bool;

    /**
     * Returns true if scope is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateScope(?Scope $scope): bool;

    /**
     * Returns true if description is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateDescription(Description $description): bool;

    /**
     * Returns true if body is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateBody(?Body $body): bool;

    /**
     * Returns true if footers are valid, otherwise throws an exception
     *
     * @param Footer[] $footers
     *
     * @throws InvalidValue
     */
    public function validateFooters(array $footers): bool;
}
