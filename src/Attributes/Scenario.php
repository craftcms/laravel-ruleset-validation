<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Scenario
{
    public function __construct(
        public string $name,
    ) {}
}
