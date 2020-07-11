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

use function trim;

/**
 * A text field used for Conventional Commits
 */
abstract class Text implements Unit
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = trim($text);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->text;
    }
}
