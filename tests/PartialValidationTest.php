<?php

declare(strict_types=1);

use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\SubsetAwareRequestRuleset;
use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\MultiFieldValidatable;
use Illuminate\Http\Request;

it('filters object validation to the requested attributes', function () {
    $ruleset = (new MultiFieldValidatable([
        'title' => 'Valid title',
    ]))->ruleset;
    $scopedRuleset = $ruleset->only(['title']);

    expect($scopedRuleset->validate())->toBe([
        'title' => 'Valid title',
    ])->and($scopedRuleset->validated())->toBe([
        'title' => 'Valid title',
    ]);
});

it('supports non throwing object validation for requested attributes', function () {
    $ruleset = (new MultiFieldValidatable([
        'title' => 'Valid title',
    ]))->ruleset;
    $slugRuleset = (clone $ruleset)->only(['slug']);

    expect($ruleset->only(['title'])->passes())->toBeTrue()
        ->and($slugRuleset->fails())->toBeTrue()
        ->and($slugRuleset->getValidator()->errors()->has('slug'))->toBeTrue();
});

it('passes the requested attributes into prepareForValidation', function () {
    $request = Request::create('/', 'POST', [
        'title' => '  Valid title  ',
        'slug' => '  untouched-slug  ',
    ]);

    $ruleset = new SubsetAwareRequestRuleset(subject: $request);
    $scopedRuleset = $ruleset->only(['title']);

    expect($scopedRuleset->validate())->toBe([
        'title' => 'Valid title',
    ])->and($scopedRuleset->preparedAttributes)->toBe(['title'])
        ->and($request->input('title'))->toBe('Valid title')
        ->and($request->input('slug'))->toBe('  untouched-slug  ');
});

it('requires only scopes to specify one or more attributes', function () {
    $ruleset = (new MultiFieldValidatable([
        'title' => 'Valid title',
    ]))->ruleset;

    $ruleset->only(null);
})->throws(TypeError::class);
