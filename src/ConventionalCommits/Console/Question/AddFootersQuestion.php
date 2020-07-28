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
 * A question asking the user whether they would like to add any footers to the
 * commit message
 */
class AddFootersQuestion extends ConfirmationQuestion
{
    public function __construct()
    {
        parent::__construct(
            'Would you like to add any footers? (e.g., Signed-off-by, See-also)',
            false,
        );
    }
}
