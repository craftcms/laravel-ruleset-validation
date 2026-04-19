<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Controllers;

use CraftCms\RulesetValidation\Attributes\Scenario;
use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\RequestScenarioRuleset;
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
