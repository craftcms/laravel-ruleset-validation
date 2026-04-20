<?php

declare(strict_types=1);

use Craft\RulesetValidation\Tests\TestClasses\Controllers\PostController;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\RedirectActionRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\RedirectingRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\RedirectRouteRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\StopOnFirstFailureRequestRuleset;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

it('stops on the first validation failure when configured', function () {
    $request = Request::create('/', 'POST');

    $ruleset = app()->make(StopOnFirstFailureRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect(array_keys($exception->errors()))->toBe(['title']);

        throw $exception;
    }
})->throws(ValidationException::class);

it('sets a custom redirect url and error bag on validation failures', function () {
    $request = Request::create('/', 'POST');

    $ruleset = app()->make(RedirectingRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->redirectTo)->toBe(url('/posts/create'))
            ->and($exception->errorBag)->toBe('createPost');

        throw $exception;
    }
})->throws(ValidationException::class);

it('sets a route based redirect on validation failures', function () {
    $request = Request::create('/', 'POST');

    $ruleset = app()->make(RedirectRouteRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->redirectTo)->toBe(route('posts.create'));

        throw $exception;
    }
})->throws(ValidationException::class);

it('sets an action based redirect on validation failures', function () {
    $request = Request::create('/', 'POST');

    $ruleset = app()->make(RedirectActionRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->redirectTo)->toBe(action(PostController::class.'@create'));

        throw $exception;
    }
})->throws(ValidationException::class);
