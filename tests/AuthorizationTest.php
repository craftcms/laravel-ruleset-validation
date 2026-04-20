<?php

declare(strict_types=1);

use Craft\RulesetValidation\Tests\TestClasses\Rulesets\AuthorizedRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\DeniedRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\UnauthorizedRequestRuleset;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

it('validates when authorization passes', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);

    $ruleset = app()->make(AuthorizedRequestRuleset::class, ['subject' => $request]);

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});

it('throws an authorization exception when authorize returns false', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);

    $ruleset = app()->make(UnauthorizedRequestRuleset::class, ['subject' => $request]);

    $ruleset->validate();
})->throws(AuthorizationException::class);

it('throws the authorization response exception when access is denied', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);

    $ruleset = app()->make(DeniedRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (AuthorizationException $exception) {
        expect($exception->getMessage())->toBe('Not allowed.');

        throw $exception;
    }
})->throws(AuthorizationException::class);
