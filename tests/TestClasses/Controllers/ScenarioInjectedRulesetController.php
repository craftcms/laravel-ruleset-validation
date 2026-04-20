<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Controllers;

use Craft\RulesetValidation\Attributes\Scenario;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\RequestScenarioRuleset;
use Illuminate\Http\JsonResponse;

class ScenarioInjectedRulesetController
{
    public function storeDraft(
        #[Scenario('draft')] RequestScenarioRuleset $ruleset,
    ): JsonResponse {
        return response()->json([
            'scenario' => $ruleset->getScenario(),
            'validated' => $ruleset->validate(),
        ]);
    }
}
