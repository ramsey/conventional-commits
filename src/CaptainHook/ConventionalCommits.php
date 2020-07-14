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

namespace Ramsey\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ConfigAction;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use Ramsey\ConventionalCommits\Exception\ConventionalException;
use Ramsey\ConventionalCommits\Parser;
use SebastianFeldmann\Git\Repository;

/**
 * Provides Conventional Commits commit message validation to CaptainHook
 */
class ConventionalCommits implements Action, Constrained
{
    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::COMMIT_MSG]);
    }

    public function execute(Config $config, IO $io, Repository $repository, ConfigAction $action): void
    {
        $message = $repository->getCommitMsg();
        $parser = new Parser();

        try {
            $parser->parse($message->getContent());
        } catch (ConventionalException $exception) {
            throw new ActionFailed(
                'The commit message is not properly formatted according '
                . 'to the Conventional Commits specification',
            );
        }
    }
}
