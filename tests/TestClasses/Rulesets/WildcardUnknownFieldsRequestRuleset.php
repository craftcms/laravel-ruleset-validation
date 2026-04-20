<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;

#[FailOnUnknownFields]
class WildcardUnknownFieldsRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.name' => ['required', 'string'],
        ];
    }
}
