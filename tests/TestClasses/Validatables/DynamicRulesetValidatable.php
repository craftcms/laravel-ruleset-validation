<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Validatables;

use CraftCms\RulesetValidation\Concerns\HasRuleset;
use CraftCms\RulesetValidation\Contracts\ValidatesWithRuleset;
use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\BasicRuleset;

class DynamicRulesetValidatable implements ValidatesWithRuleset
{
    use HasRuleset;

    public function __construct(
        private readonly array $data = [],
    ) {}

    public function validationData(): array
    {
        return $this->data;
    }

    public function ruleset(): string
    {
        return BasicRuleset::class;
    }
}
