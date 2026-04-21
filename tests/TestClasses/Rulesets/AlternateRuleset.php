<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;

class AlternateRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'slug' => ['required'],
        ];
    }
}
