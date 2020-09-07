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
use CaptainHook\App\Config\Action as ActionConfig;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use Ramsey\ConventionalCommits\Console\Command\PrepareCommand;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

use function trim;

/**
 * During the prepare-commit-msg Git hook, this prompts the user for input and
 * builds a valid Conventional Commits commit message
 */
class PrepareConventionalCommit implements Action, Constrained
{
    private PrepareCommand $prepareCommand;

    public function __construct(?PrepareCommand $prepareCommand = null)
    {
        $this->prepareCommand = $prepareCommand ?? new PrepareCommand();
    }

    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::PREPARE_COMMIT_MSG]);
    }

    public function execute(
        Config $config,
        IO $io,
        Repository $repository,
        ActionConfig $action
    ): void {
        if (!$io->isInteractive()) {
            return;
        }

        $commitMessage = $repository->getCommitMsg();
        if (trim($commitMessage->getContent()) !== '') {
            // If we already have a commit message (maybe we used -m),
            // do not proceed with prompting the user for input.
            return;
        }

        $this->prepareCommand->run(new Input($io), new Output($io));

        $message = $this->prepareCommand->getMessage();

        if ($message === null) {
            return;
        }

        $commitMessage = new CommitMessage($message->toString());
        $repository->setCommitMsg($commitMessage);
    }
}
