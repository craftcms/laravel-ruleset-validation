<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Validatables;

use Craft\RulesetValidation\Attributes\Ruleset as RulesetAttribute;
use Craft\RulesetValidation\Concerns\HasRuleset;
use Craft\RulesetValidation\Contracts\ValidatesWithRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\BasicRuleset;

#[RulesetAttribute(class: BasicRuleset::class)]
class NamedAttributedValidatable implements ValidatesWithRuleset
{
    use HasRuleset;

    public function __construct(
        private readonly array $data = [],
    ) {}

    public function validationData(): array
    {
        return $this->data;
    }
}
