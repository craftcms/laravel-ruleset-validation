<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;

class CustomValidatorRequestRuleset extends Ruleset
{
    public function validator(ValidationFactory $factory): Validator
    {
        return $factory->make($this->validationData(), [
            'name' => ['required', 'string'],
        ]);
    }
}
