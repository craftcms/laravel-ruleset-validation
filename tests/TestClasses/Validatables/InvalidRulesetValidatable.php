<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Validatables;

use Craft\RulesetValidation\Concerns\HasRuleset;
use Craft\RulesetValidation\Contracts\ValidatesWithRuleset;
use Illuminate\Database\Eloquent\Model;

class InvalidRulesetValidatable implements ValidatesWithRuleset
{
    use HasRuleset;

    public function validationData(): array
    {
        return [];
    }

    public function ruleset(): string
    {
        return Model::class;
    }
}
