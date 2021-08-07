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

namespace Ramsey\ConventionalCommits\Configuration;

use Jawira\CaseConverter\CaseConverter;
use Ramsey\ConventionalCommits\Converter\LetterCaseConverter;
use Ramsey\ConventionalCommits\Exception\InvalidArgument;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;
use Ramsey\ConventionalCommits\String\LetterCase;
use Ramsey\ConventionalCommits\Validator\DefaultMessageValidator;
use Ramsey\ConventionalCommits\Validator\MessageValidator;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_int;
use function preg_match;

/**
 * Default configuration for Conventional Commits commit messages
 */
class DefaultConfiguration implements Configuration
{
    private ?string $typeCase;

    /** @var string[] */
    private array $types;

    private ?string $scopeCase;
    private bool $scopeRequired;

    /** @var string[] */
    private array $scopes;

    private ?string $descriptionCase;
    private ?string $descriptionEndMark;
    private bool $bodyRequired;
    private ?int $bodyWrapWidth = null;

    /** @var string[] */
    private array $requiredFooters;

    /** @var LetterCaseConverter[] */
    private array $letterCaseConverters = [];

    private ?MessageValidator $messageValidator = null;

    /**
     * @param mixed[] $options
     */
    public function __construct(array $options = [])
    {
        $this->typeCase = $this->caseIfValid($options, 'typeCase');
        $this->types = $this->typesIfValid($options['types'] ?? []);
        $this->scopeCase = $this->caseIfValid($options, 'scopeCase');
        $this->scopeRequired = (bool) ($options['scopeRequired'] ?? false);
        $this->scopes = $this->scopesIfValid($options['scopes'] ?? []);
        $this->descriptionCase = $this->caseIfValid($options, 'descriptionCase');
        $this->descriptionEndMark = $this->endMarkIfValid($options['descriptionEndMark'] ?? null);
        $this->bodyRequired = (bool) ($options['bodyRequired'] ?? false);
        $this->requiredFooters = $this->requiredFootersIfValid($options['requiredFooters'] ?? []);

        if (is_int($options['bodyWrapWidth'] ?? null)) {
            $this->bodyWrapWidth = (int) $options['bodyWrapWidth'];
        }
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'typeCase' => $this->getTypeCase(),
            'types' => $this->getTypes(),
            'scopeCase' => $this->getScopeCase(),
            'scopeRequired' => $this->isScopeRequired(),
            'scopes' => $this->getScopes(),
            'descriptionCase' => $this->getDescriptionCase(),
            'descriptionEndMark' => $this->getDescriptionEndMark(),
            'bodyRequired' => $this->isBodyRequired(),
            'bodyWrapWidth' => $this->getBodyWrapWidth(),
            'requiredFooters' => $this->getRequiredFooters(),
        ];
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getTypeCase(): ?string
    {
        return $this->typeCase;
    }

    /**
     * @inheritDoc
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function getScopeCase(): ?string
    {
        return $this->scopeCase;
    }

    public function isScopeRequired(): bool
    {
        return $this->scopeRequired;
    }

    /**
     * @inheritDoc
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getDescriptionCase(): ?string
    {
        return $this->descriptionCase;
    }

    public function getDescriptionEndMark(): ?string
    {
        return $this->descriptionEndMark;
    }

    public function isBodyRequired(): bool
    {
        return $this->bodyRequired;
    }

    public function getBodyWrapWidth(): ?int
    {
        return $this->bodyWrapWidth;
    }

    /**
     * @inheritDoc
     */
    public function getRequiredFooters(): array
    {
        return $this->requiredFooters;
    }

    /**
     * Adds a letter case converter instance, configured for a specific case
     */
    public function addLetterCaseConverter(LetterCaseConverter $letterCaseConverter): void
    {
        $this->letterCaseConverters[(string) $letterCaseConverter->getCase()] = $letterCaseConverter;
    }

    public function getLetterCaseConverter(?string $case): LetterCaseConverter
    {
        if (!array_key_exists((string) $case, $this->letterCaseConverters)) {
            $caseConverter = new CaseConverter();
            $this->letterCaseConverters[(string) $case] = new LetterCaseConverter($caseConverter, $case);
        }

        return $this->letterCaseConverters[(string) $case];
    }

    /**
     * Sets or overrides the message validator to use with this configuration
     */
    public function setMessageValidator(MessageValidator $messageValidator): void
    {
        $this->messageValidator = $messageValidator;
    }

    public function getMessageValidator(): MessageValidator
    {
        if ($this->messageValidator === null) {
            $this->messageValidator = new DefaultMessageValidator($this);
        }

        return $this->messageValidator;
    }

    /**
     * @param mixed[] $options
     */
    private function caseIfValid(array $options, string $parameter): ?string
    {
        /** @var string|null $value */
        $value = $options[$parameter] ?? null;

        if ($value !== null && !in_array($value, LetterCase::CASES)) {
            throw new InvalidArgument("'{$value}' is not a valid case for {$parameter}.");
        }

        return $value;
    }

    /**
     * @param mixed $types
     *
     * @return string[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    private function typesIfValid($types): array
    {
        $validTypes = [];

        if (!is_array($types)) {
            $types = [$types];
        }

        /** @var mixed $type */
        foreach ($types as $type) {
            try {
                $validTypes[] = (new Type((string) $type))->toString();
            } catch (InvalidArgument $exception) {
                throw new InvalidArgument(
                    "'{$type}' is not a valid type; types may contain only "
                    . 'alphanumeric characters, underscores, and dashes.',
                );
            }
        }

        return $validTypes;
    }

    /**
     * @param mixed $scopes
     *
     * @return string[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    private function scopesIfValid($scopes): array
    {
        $validScopes = [];

        if (!is_array($scopes)) {
            $scopes = [$scopes];
        }

        /** @var mixed $scope */
        foreach ($scopes as $scope) {
            try {
                $validScopes[] = (new Scope((string) $scope))->toString();
            } catch (InvalidArgument $exception) {
                throw new InvalidArgument(
                    "'{$scope}' is not a valid scope; scopes may contain only "
                    . 'alphanumeric characters, underscores, and dashes.',
                );
            }
        }

        return $validScopes;
    }

    /**
     * @param mixed $requiredFooters
     *
     * @return string[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    private function requiredFootersIfValid($requiredFooters): array
    {
        $validFooters = [];

        if (!is_array($requiredFooters)) {
            $requiredFooters = [$requiredFooters];
        }

        /** @var mixed $footer */
        foreach ($requiredFooters as $footer) {
            try {
                $validFooters[] = (new Footer((string) $footer, 'placeholder'))->getToken();
            } catch (InvalidArgument $exception) {
                throw new InvalidArgument(
                    "'{$footer}' is not a valid footer token; footer tokens may contain only "
                    . "alphanumeric characters and dashes or the phrase 'BREAKING CHANGE'.",
                );
            }
        }

        return $validFooters;
    }

    /**
     * @param mixed $endMark
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    private function endMarkIfValid($endMark): ?string
    {
        if ($endMark === null) {
            return null;
        }

        $endMark = (string) $endMark;

        if (!preg_match('/^[[:punct:]]?$/u', $endMark)) {
            throw new InvalidArgument("'{$endMark}' is not a valid punctuation character.");
        }

        return $endMark;
    }
}
