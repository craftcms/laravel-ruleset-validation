<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use Illuminate\Auth\Access\Response;

class DeniedRequestRuleset extends RequestRuleset
{
    public function authorize(): Response
    {
        return Response::deny('Not allowed.');
    }
}
