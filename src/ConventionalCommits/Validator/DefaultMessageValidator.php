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

use Ramsey\ConventionalCommits\Configuration\Configurable;
use Ramsey\ConventionalCommits\Configuration\ConfigurableTool;
use Ramsey\ConventionalCommits\Configuration\Configuration;
use Ramsey\ConventionalCommits\Exception\InvalidValue;
use Ramsey\ConventionalCommits\Message;
use Ramsey\ConventionalCommits\Message\Body;
use Ramsey\ConventionalCommits\Message\Description;
use Ramsey\ConventionalCommits\Message\Footer;
use Ramsey\ConventionalCommits\Message\Scope;
use Ramsey\ConventionalCommits\Message\Type;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;
use function strlen;
use function trim;

/**
 * DefaultMessageValidator validates commit messages according to the
 * Conventional Commits specification, as well as additional configuration
 */
class DefaultMessageValidator implements Configurable, MessageValidator
{
    use ConfigurableTool;

    public function __construct(Configuration $configuration)
    {
        $this->setConfiguration($configuration);
    }

    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        if (!$value instanceof Message) {
            return false;
        }

        try {
            $this->validateType($value->getType());
            $this->validateScope($value->getScope());
            $this->validateDescription($value->getDescription());
            $this->validateBody($value->getBody());
            $this->validateFooters($value->getFooters());
        } catch (InvalidValue $exception) {
            return false;
        }

        return true;
    }

    /**
     * @throws InvalidValue
     *
     * @inheritDoc
     */
    public function isValidOrException($value): bool
    {
        if (!$value instanceof Message) {
            $type = is_object($value) ? get_class($value) : gettype($value);

            throw new InvalidValue(sprintf(
                'Expected an instance of %s; received %s.',
                Message::class,
                $type,
            ));
        }

        $this->validateType($value->getType());
        $this->validateScope($value->getScope());
        $this->validateDescription($value->getDescription());
        $this->validateBody($value->getBody());
        $this->validateFooters($value->getFooters());

        return true;
    }

    /**
     * Returns true if type is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateType(Type $type): bool
    {
        $typeCaseValidator = new LetterCaseValidator($this->getConfiguration()->getTypeCase());
        $typeCaseValidator->setConfiguration($this->getConfiguration());

        $typeValidator = new TypeValidator();
        $typeValidator->setConfiguration($this->getConfiguration());

        $type->addValidator($typeCaseValidator);
        $type->addValidator($typeValidator);
        $type->validate();

        return true;
    }

    /**
     * Returns true if scope is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateScope(?Scope $scope): bool
    {
        if ($scope === null && $this->getConfiguration()->isScopeRequired()) {
            throw new InvalidValue('You must provide a scope.');
        }

        if ($scope === null) {
            return true;
        }

        $scopeCaseValidator = new LetterCaseValidator($this->getConfiguration()->getScopeCase());
        $scopeCaseValidator->setConfiguration($this->getConfiguration());

        $scopeValidator = new ScopeValidator();
        $scopeValidator->setConfiguration($this->getConfiguration());

        $scope->addValidator($scopeCaseValidator);
        $scope->addValidator($scopeValidator);
        $scope->validate();

        return true;
    }

    /**
     * Returns true if description is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateDescription(Description $description): bool
    {
        $descriptionCaseValidator = new LetterCaseValidator($this->getConfiguration()->getDescriptionCase());
        $descriptionCaseValidator->setConfiguration($this->getConfiguration());

        $descriptionEndMarkValidator = new EndMarkValidator($this->getConfiguration()->getDescriptionEndMark());

        $description->addValidator($descriptionCaseValidator);
        $description->addValidator($descriptionEndMarkValidator);
        $description->validate();

        return true;
    }

    /**
     * Returns true if body is valid, otherwise throws an exception
     *
     * @throws InvalidValue
     */
    public function validateBody(?Body $body): bool
    {
        $isEmpty = $body === null || strlen(trim($body->toString())) === 0;

        if ($isEmpty && $this->getConfiguration()->isBodyRequired()) {
            throw new InvalidValue('You must provide a body.');
        }

        return true;
    }

    /**
     * Returns true if footers are valid, otherwise throws an exception
     *
     * @param Footer[] $footers
     *
     * @throws InvalidValue
     */
    public function validateFooters(array $footers): bool
    {
        $requiredFootersValidator = new RequiredFootersValidator();
        $requiredFootersValidator->setConfiguration($this->getConfiguration());
        $requiredFootersValidator->isValidOrException($footers);

        return true;
    }
}
