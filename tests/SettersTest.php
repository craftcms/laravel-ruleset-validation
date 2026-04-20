<?php

declare(strict_types=1);

use Craft\RulesetValidation\Tests\TestClasses\Rulesets\BasicRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\BareValidatable;

it('allows injecting a validator instance', function () {
    $ruleset = app()->make(BasicRuleset::class, [
        'subject' => new BareValidatable,
    ]);

    $validator = validator(
        ['title' => 'Injected title'],
        ['title' => ['required']],
    );

    expect($ruleset->setValidator($validator))->toBe($ruleset)
        ->and($ruleset->validated())->toBe([
            'title' => 'Injected title',
        ]);
});

it('returns itself when setting the container and redirector', function () {
    $ruleset = app()->make(BasicRuleset::class, [
        'subject' => new BareValidatable,
    ]);

    expect($ruleset->setContainer(app()))->toBe($ruleset)
        ->and($ruleset->setRedirector(app('redirect')))->toBe($ruleset);
});
