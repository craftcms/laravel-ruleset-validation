<?php

declare(strict_types=1);

use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\PrecognitiveRequestRuleset;
use CraftCms\RulesetValidation\Tests\TestClasses\Rulesets\RequestRuleset;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('returns validated data when validating a request object', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);

    $ruleset = app()->make(RequestRuleset::class, ['subject' => $request]);

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});

it('can be manually instantiated with a request subject', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);

    $ruleset = new RequestRuleset(subject: $request);

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});

it('throws validation errors when validating a request object', function () {
    $request = Request::create(
        '/',
        'POST',
        [],
        [],
        [],
        ['HTTP_REFERER' => 'http://localhost/previous'],
    );

    $ruleset = app()->make(RequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors())->toBeArray()
            ->and($exception->errors()['title'])->not()->toBeEmpty()
            ->and($exception->redirectTo)->toBe('http://localhost');

        throw $exception;
    }
})->throws(ValidationException::class);

it('short circuits successful precognitive requests', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
    ]);
    $request->headers->set('Precognition', 'true');
    $request->headers->set('Precognition-Validate-Only', 'title');
    $request->attributes->set('precognitive', true);

    $ruleset = app()->make(PrecognitiveRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe(204);

        throw $exception;
    }
})->throws(HttpException::class);

it('registers the precognition hook once per validator instance', function () {
    $request = Request::create('/', 'POST', []);
    $request->headers->set('Precognition', 'true');
    $request->headers->set('Precognition-Validate-Only', 'title');
    $request->attributes->set('precognitive', true);

    $ruleset = new PrecognitiveRequestRuleset(subject: $request);
    $validator = $ruleset->getValidator();
    $afterCallbacks = new ReflectionProperty($validator, 'after');
    $initialCount = count($afterCallbacks->getValue($validator));

    expect($initialCount)->toBeGreaterThan(0)
        ->and($ruleset->fails())->toBeTrue()
        ->and(count($afterCallbacks->getValue($validator)))->toBe($initialCount);
});
