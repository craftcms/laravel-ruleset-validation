<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;

#[FailOnUnknownFields]
class UnknownFieldsRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
        ];
    }
}
