<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Controllers;

use Craft\RulesetValidation\Tests\TestClasses\Rulesets\RequestRuleset;
use Illuminate\Http\JsonResponse;

class InjectedRulesetController
{
    public function store(RequestRuleset $ruleset): JsonResponse
    {
        return response()->json($ruleset->validate());
    }
}
