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

use CaptainHook\App\Console\IO;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Wraps CaptainHook console IO for use with symfony/console
 */
class Output extends ConsoleOutput
{
    private IO $captainHookIO;

    public function __construct(IO $captainHookIO)
    {
        parent::__construct($this->translateCaptainHookVerbosity($captainHookIO));
        $this->captainHookIO = $captainHookIO;
    }

    protected function doWrite(string $message, bool $newline): void
    {
        $this->captainHookIO->write($message, $newline);
    }

    private function translateCaptainHookVerbosity(IO $captainHookIO): int
    {
        if ($captainHookIO->isDebug()) {
            return self::VERBOSITY_DEBUG;
        }

        if ($captainHookIO->isVeryVerbose()) {
            return self::VERBOSITY_VERY_VERBOSE;
        }

        if ($captainHookIO->isVerbose()) {
            return self::VERBOSITY_VERBOSE;
        }

        return self::VERBOSITY_NORMAL;
    }
}
