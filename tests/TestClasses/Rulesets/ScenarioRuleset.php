<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;

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
