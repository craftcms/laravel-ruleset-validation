<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;

class RequestScenarioRuleset extends Ruleset
{
    public function rules(): array
    {
        if ($this->inScenarios('draft')) {
            return [];
        }

        return [
            'title' => ['required'],
        ];
    }
}
