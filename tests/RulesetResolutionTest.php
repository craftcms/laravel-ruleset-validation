<?php

declare(strict_types=1);

use CraftCms\RulesetValidation\Ruleset;
use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\BasicRuleset;
use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\AttributedValidatable;
use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\BareValidatable;
use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\DynamicRulesetValidatable;
use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\InvalidRulesetValidatable;
use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\NamedAttributedValidatable;

it('returns the base ruleset when none is configured', function () {
    expect((new BareValidatable)->ruleset)->toBeInstanceOf(Ruleset::class);
});

it('resolves the ruleset from a class attribute', function () {
    expect((new AttributedValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('resolves the ruleset from a named class attribute argument', function () {
    expect((new NamedAttributedValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('resolves the ruleset from a ruleset method', function () {
    expect((new DynamicRulesetValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('memoizes the resolved ruleset instance', function () {
    $validatable = new AttributedValidatable;

    expect($validatable->ruleset)->toBe($validatable->ruleset);
});

it('throws when the resolved ruleset class is invalid', function () {
    new InvalidRulesetValidatable()->ruleset;
})->throws(InvalidArgumentException::class, 'The rules class must be an instance of '.Ruleset::class);
