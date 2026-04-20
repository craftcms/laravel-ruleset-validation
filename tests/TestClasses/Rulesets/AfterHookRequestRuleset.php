<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Contracts\Validation\Validator;

class AfterHookRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
        ];
    }

    public function after(Validator $validator): array
    {
        return [
            function (Validator $validator): void {
                if (($this->validationData()['title'] ?? null) === 'blocked') {
                    $validator->errors()->add('title', 'This title is blocked.');
                }
            },
        ];
    }
}
