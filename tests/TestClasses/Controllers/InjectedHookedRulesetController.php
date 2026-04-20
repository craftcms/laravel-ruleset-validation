<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Controllers;

use Craft\RulesetValidation\Tests\TestClasses\Rulesets\HookedRequestRuleset;
use Illuminate\Http\JsonResponse;

class InjectedHookedRulesetController
{
    public function store(HookedRequestRuleset $ruleset): JsonResponse
    {
        return response()->json($ruleset->validate());
    }
}
