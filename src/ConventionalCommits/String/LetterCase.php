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

namespace Ramsey\ConventionalCommits\String;

/**
 * Provides constants for letter case identifiers
 */
final class LetterCase
{
    public const CASE_ADA = 'ada';
    public const CASE_CAMEL = 'camel';
    public const CASE_COBOL = 'cobol';
    public const CASE_DOT = 'dot';
    public const CASE_KEBAB = 'kebab';
    public const CASE_LOWER = 'lower';
    public const CASE_MACRO = 'macro';
    public const CASE_PASCAL = 'pascal';
    public const CASE_SENTENCE = 'sentence';
    public const CASE_SNAKE = 'snake';
    public const CASE_TITLE = 'title';
    public const CASE_TRAIN = 'train';
    public const CASE_UPPER = 'upper';

    public const CASES = [
        self::CASE_ADA,
        self::CASE_CAMEL,
        self::CASE_COBOL,
        self::CASE_DOT,
        self::CASE_KEBAB,
        self::CASE_LOWER,
        self::CASE_MACRO,
        self::CASE_PASCAL,
        self::CASE_SENTENCE,
        self::CASE_SNAKE,
        self::CASE_TITLE,
        self::CASE_TRAIN,
        self::CASE_UPPER,
    ];
}
