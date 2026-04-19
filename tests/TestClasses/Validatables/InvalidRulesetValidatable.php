<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Validatables;

use CraftCms\RulesetValidation\Concerns\HasRuleset;
use CraftCms\RulesetValidation\Contracts\ValidatesWithRuleset;
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
