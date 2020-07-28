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

namespace Ramsey\ConventionalCommits\Console\Question;

use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * A question asking the user whether the commit affects any open issues or
 * tickets in an issue-tracker system
 */
class AffectsOpenIssuesQuestion extends ConfirmationQuestion
{
    public function __construct()
    {
        parent::__construct('Does this change affect any open issues?', false);
    }
}
