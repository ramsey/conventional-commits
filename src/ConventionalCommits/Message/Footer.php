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

namespace Ramsey\ConventionalCommits\Message;

use Ramsey\ConventionalCommits\Exception\InvalidArgument;

use function implode;
use function in_array;
use function preg_match;
use function sprintf;
use function trim;

use const PHP_EOL;

/**
 * A Conventional Commits footer
 *
 * From the Conventional Commits 1.0.0 specification:
 *
 * > 8. One or more footers MAY be provided one blank line after the body. Each
 * > footer MUST consist of a word token, followed by either a `:<space>` or
 * > `<space>#` separator, followed by a string value.
 * >
 * > 9. A footer’s token MUST use `-` in place of whitespace characters, e.g.,
 * > `Acked-by` (this helps differentiate the footer section from a
 * > multi-paragraph body). An exception is made for `BREAKING CHANGE`, which
 * > MAY also be used as a token.
 * >
 * > 10. A footer’s value MAY contain spaces and newlines, and parsing MUST
 * > terminate when the next valid footer token/separator pair is observed.
 * >
 * > 11. Breaking changes MUST be indicated in the type/scope prefix of a
 * > commit, or as an entry in the footer.
 * >
 * > 12. If included as a footer, a breaking change MUST consist of the
 * > uppercase text BREAKING CHANGE, followed by a colon, space, and
 * > description, e.g., *BREAKING CHANGE: environment variables now take
 * > precedence over config files.*
 * >
 * > 16. BREAKING-CHANGE MUST be synonymous with BREAKING CHANGE, when used as a
 * > token in a footer.
 *
 * @link https://www.conventionalcommits.org/en/v1.0.0/#specification Conventional Commits
 */
class Footer implements Unit
{
    public const TOKEN_BREAKING_CHANGE = 'BREAKING CHANGE';

    public const SEPARATOR_COLON = ': ';
    public const SEPARATOR_HASH = ' #';

    private const SEPARATORS = [
        self::SEPARATOR_COLON,
        self::SEPARATOR_HASH,
    ];

    private const TOKEN_PATTERN = '/^(?:BREAKING CHANGE|[a-zA-Z0-9][\w-]+)$/iu';
    private const VALUE_PATTERN = '/^(?:(?!(?:\r\n|\n|\r)'
        . '(?:BREAKING CHANGE|[a-zA-Z0-9][\w-]+)\ *(?:\:\ *|\#\w)).)*$/siu';

    private string $token;
    private string $value;
    private string $separator;

    public function __construct(
        string $token,
        string $value,
        string $separator = self::SEPARATOR_COLON
    ) {
        if (!preg_match(self::TOKEN_PATTERN, $token)) {
            throw new InvalidArgument("Token '{$token}' is invalid");
        }

        // Prepend a newline to assert it doesn't begin with any footer tokens.
        if (!preg_match(self::VALUE_PATTERN, PHP_EOL . $value)) {
            throw new InvalidArgument('Value contains unexpected footer tokens');
        }

        if (!in_array($separator, self::SEPARATORS)) {
            throw new InvalidArgument(sprintf(
                "Separator '%s' is invalid; expected one of ['%s']",
                $separator,
                implode("', '", self::SEPARATORS),
            ));
        }

        // Normalize "breaking change" token.
        if (
            strcasecmp($token, 'BREAKING CHANGE') === 0
            || strcasecmp($token, 'BREAKING-CHANGE') === 0
        ) {
            $token = self::TOKEN_BREAKING_CHANGE;
        }

        $this->token = trim($token);
        $this->value = trim($value);
        $this->separator = $separator;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->token . $this->separator . $this->value;
    }
}
