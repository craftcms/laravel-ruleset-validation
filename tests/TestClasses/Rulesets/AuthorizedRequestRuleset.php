<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

class AuthorizedRequestRuleset extends RequestRuleset
{
    public function authorize(): bool
    {
        return true;
    }
}
