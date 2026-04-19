<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;

class MultiFieldRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
        ];
    }
}
