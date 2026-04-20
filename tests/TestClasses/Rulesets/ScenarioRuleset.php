<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;

class ScenarioRuleset extends Ruleset
{
    public function rules(): array
    {
        if ($this->inScenarios('none')) {
            return [];
        }

        return [
            'title' => ['required'],
        ];
    }
}
