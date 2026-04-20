<?php

declare(strict_types=1);

use Craft\RulesetValidation\Ruleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\AfterHookRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\CustomMessageRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\CustomValidatorRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\HookedRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\WithValidatorRequestRuleset;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

it('runs the prepare and passed validation hooks', function () {
    $request = Request::create('/', 'POST', [
        'title' => '  Valid title  ',
    ]);

    $ruleset = app()->make(HookedRequestRuleset::class, ['subject' => $request]);

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ])->and($ruleset->prepared)->toBeTrue()
        ->and($ruleset->passed)->toBeTrue();
});

it('allows withValidator to modify validation behavior', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);

    $ruleset = app()->make(WithValidatorRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors()['slug'])->not()->toBeEmpty();

        throw $exception;
    }
})->throws(ValidationException::class);

it('allows after hooks to add validation errors', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'blocked',
    ]);

    $ruleset = app()->make(AfterHookRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors()['title'])->toContain('This title is blocked.');

        throw $exception;
    }
})->throws(ValidationException::class);

it('supports custom validator instances', function () {
    Ruleset::flushState();

    $request = Request::create('/', 'POST', [
        'name' => 'Taylor',
    ]);

    $ruleset = app()->make(CustomValidatorRequestRuleset::class, ['subject' => $request]);

    expect($ruleset->validate())->toBe([
        'name' => 'Taylor',
    ]);
});

it('uses custom messages and attribute names', function () {
    $request = Request::create('/', 'POST');

    $ruleset = app()->make(CustomMessageRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors()['title'])->toContain('A post title is required.');

        throw $exception;
    }
})->throws(ValidationException::class);
