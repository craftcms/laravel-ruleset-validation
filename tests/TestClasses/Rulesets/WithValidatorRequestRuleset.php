<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;
use Illuminate\Contracts\Validation\Validator;

class WithValidatorRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'slug' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->sometimes('slug', ['required'], fn (): bool => true);
    }
}
