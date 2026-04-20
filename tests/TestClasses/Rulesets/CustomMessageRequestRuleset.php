<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;

class CustomMessageRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A :attribute is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'post title',
        ];
    }
}
