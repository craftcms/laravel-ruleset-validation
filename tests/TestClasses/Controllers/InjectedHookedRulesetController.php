<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Controllers;

use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\HookedRequestRuleset;
use Illuminate\Http\JsonResponse;

class InjectedHookedRulesetController
{
    public function store(HookedRequestRuleset $ruleset): JsonResponse
    {
        return response()->json($ruleset->validate());
    }
}
