<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;

class BasicRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
        ];
    }
}
