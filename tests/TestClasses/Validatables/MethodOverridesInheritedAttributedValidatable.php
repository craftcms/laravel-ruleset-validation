<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Validatables;

use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\AlternateRuleset;

class MethodOverridesInheritedAttributedValidatable extends AttributedValidatable
{
    public function ruleset(): string
    {
        return AlternateRuleset::class;
    }
}
