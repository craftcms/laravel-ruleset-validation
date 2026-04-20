<?php

declare(strict_types=1);

use Craft\RulesetValidation\Ruleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\RequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\UnknownFieldsOptOutRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\UnknownFieldsRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Rulesets\WildcardUnknownFieldsRequestRuleset;
use Craft\RulesetValidation\Tests\TestClasses\Validatables\AttributedValidatable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

it('rejects unknown fields when configured on the ruleset', function () {
    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
        'extra' => 'Unexpected',
    ]);

    $ruleset = app()->make(UnknownFieldsRequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors()['extra'])->not()->toBeEmpty();

        throw $exception;
    }
})->throws(ValidationException::class);

it('allows known wildcard nested fields and rejects unknown nested fields', function () {
    $validRequest = Request::create('/', 'POST', [
        'items' => [
            ['name' => 'First'],
        ],
    ]);

    $validRuleset = app()->make(WildcardUnknownFieldsRequestRuleset::class, ['subject' => $validRequest]);

    expect($validRuleset->validate())->toBe([
        'items' => [
            ['name' => 'First'],
        ],
    ]);

    $invalidRequest = Request::create('/', 'POST', [
        'items' => [
            ['name' => 'First', 'description' => 'Unexpected'],
        ],
    ]);

    $invalidRuleset = app()->make(WildcardUnknownFieldsRequestRuleset::class, ['subject' => $invalidRequest]);

    try {
        $invalidRuleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors()['items.0.description'])->not()->toBeEmpty();

        throw $exception;
    }
})->throws(ValidationException::class);

it('supports enabling and flushing global unknown field rejection', function () {
    Ruleset::failOnUnknownFields();

    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
        'extra' => 'Unexpected',
    ]);

    $ruleset = app()->make(RequestRuleset::class, ['subject' => $request]);

    try {
        $ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors()['extra'])->not()->toBeEmpty();
    }

    Ruleset::flushState();

    $resetRuleset = app()->make(RequestRuleset::class, ['subject' => $request]);

    expect($resetRuleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});

it('lets a class level attribute opt out of global unknown field rejection', function () {
    Ruleset::failOnUnknownFields();

    $request = Request::create('/', 'POST', [
        'title' => 'Valid title',
        'extra' => 'Unexpected',
    ]);

    $ruleset = app()->make(UnknownFieldsOptOutRequestRuleset::class, ['subject' => $request]);

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});

it('does not apply unknown field rejection to non request objects', function () {
    Ruleset::failOnUnknownFields();

    $ruleset = (new AttributedValidatable([
        'title' => 'Valid title',
        'extra' => 'Unexpected',
    ]))->ruleset;

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});
