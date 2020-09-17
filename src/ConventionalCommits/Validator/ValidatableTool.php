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

/**
 * This tool provides standard functionality for instances implementing Validatable
 */
trait ValidatableTool
{
    /**
     * @var Validator[]
     */
    private array $validator = [];

    public function addValidator(Validator $validator): void
    {
        $this->validator[] = $validator;
    }

    /**
     * @return Validator[]
     */
    public function getValidators(): array
    {
        return $this->validator;
    }

    public function validate(): bool
    {
        foreach ($this->getValidators() as $validator) {
            $validator->isValidOrException($this->toString());
        }

        return true;
    }
}
