<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Http\Request;

class SubsetAwareRequestRuleset extends Ruleset
{
    /**
     * @var array<string>|null
     */
    public ?array $preparedAttributes = null;

    protected function prepareForValidation(): void
    {
        $this->preparedAttributes = $this->validationAttributes;

        if (! $this->subject instanceof Request) {
            return;
        }

        foreach ($this->validationAttributes ?? ['title', 'slug'] as $attribute) {
            $value = $this->subject->input($attribute);

            if (! is_string($value)) {
                continue;
            }

            $this->subject->merge([
                $attribute => trim($value),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
        ];
    }
}
