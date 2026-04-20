<?php

declare(strict_types=1);

use Craft\RulesetValidation\Tests\TestClasses\Validatables\AttributedValidatable;
use Illuminate\Support\ValidatedInput;
use Illuminate\Validation\ValidationException;

it('throws validation errors for invalid validatable objects', function () {
    $validatable = new AttributedValidatable;

    try {
        $validatable->ruleset->validate();
    } catch (ValidationException $exception) {
        expect($exception->errors())->toBeArray()
            ->and($exception->errors()['title'])->not()->toBeEmpty();

        throw $exception;
    }
})->throws(ValidationException::class);

it('returns validated data for validatable objects and reuses the validator instance', function () {
    $validatable = new AttributedValidatable([
        'title' => 'Valid title',
    ]);

    $ruleset = $validatable->ruleset;

    expect($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ])->and($ruleset->validate())->toBe([
        'title' => 'Valid title',
    ]);
});

it('returns validated data by key and default after validation', function () {
    $ruleset = (new AttributedValidatable([
        'title' => 'Valid title',
    ]))->ruleset;

    $ruleset->validate();

    expect($ruleset->validated())->toBe([
        'title' => 'Valid title',
    ])->and($ruleset->validated('title'))->toBe('Valid title')
        ->and($ruleset->validated('missing', 'fallback'))->toBe('fallback');
});

it('returns validated input containers and subsets', function () {
    $ruleset = (new AttributedValidatable([
        'title' => 'Valid title',
    ]))->ruleset;

    $ruleset->validate();

    expect($ruleset->safe())->toBeInstanceOf(ValidatedInput::class)
        ->and($ruleset->safe(['title']))->toBe([
            'title' => 'Valid title',
        ]);
});
