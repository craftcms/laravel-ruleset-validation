<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;

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
