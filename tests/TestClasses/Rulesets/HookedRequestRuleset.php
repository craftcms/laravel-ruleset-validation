<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Http\Request;

class HookedRequestRuleset extends Ruleset
{
    public bool $prepared = false;

    public bool $passed = false;

    protected function prepareForValidation(): void
    {
        $this->prepared = true;

        if ($this->subject instanceof Request) {
            $this->subject->merge([
                'title' => trim((string) $this->subject->input('title')),
            ]);
        }
    }

    protected function passedValidation(): void
    {
        $this->passed = true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
        ];
    }
}
