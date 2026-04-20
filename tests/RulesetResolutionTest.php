<?php

declare(strict_types=1);

use Craft\RulesetValidation\Tests\TestClasses\Rulesets\BasicRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\AttributedValidatable;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\BareValidatable;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\DynamicRulesetValidatable;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\InheritedAttributedValidatable;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\InvalidRulesetValidatable;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\NamedAttributedValidatable;

it('returns false when no ruleset is configured', function () {
    expect((new BareValidatable)->ruleset)->toBeFalse();
});

it('resolves the ruleset from a class attribute', function () {
    expect((new AttributedValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('resolves the ruleset from a named class attribute argument', function () {
    expect((new NamedAttributedValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('resolves the ruleset from an inherited class attribute', function () {
    expect((new InheritedAttributedValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('resolves the ruleset from a ruleset method', function () {
    expect((new DynamicRulesetValidatable)->ruleset)->toBeInstanceOf(BasicRuleset::class);
});

it('memoizes the resolved ruleset instance', function () {
    $validatable = new AttributedValidatable;

    expect($validatable->ruleset)->toBe($validatable->ruleset);
});

it('returns false when the resolved ruleset class is invalid', function () {
    expect((new InvalidRulesetValidatable)->ruleset)->toBeFalse();
});
