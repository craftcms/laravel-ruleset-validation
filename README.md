# Laravel Ruleset Validation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/craftcms/laravel-ruleset-validation.svg?style=flat-square)](https://packagist.org/packages/craftcms/laravel-ruleset-validation)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/craftcms/laravel-ruleset-validation/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/craftcms/laravel-ruleset-validation/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/craftcms/laravel-ruleset-validation/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/craftcms/laravel-ruleset-validation/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/craftcms/laravel-ruleset-validation.svg?style=flat-square)](https://packagist.org/packages/craftcms/laravel-ruleset-validation)

Move validation out of controllers, form requests, and ad hoc validators into reusable ruleset objects.

This package provides a `Ruleset` base class that can validate either:

- any object implementing `ValidatesWithRuleset`
- a regular `Illuminate\Http\Request`

That makes it useful for DTOs, actions, domain objects, and controllers that want `FormRequest`-style validation without requiring a dedicated `FormRequest` subclass for every workflow.

## Installation

Install the package via Composer:

```bash
composer require craft/laravel-ruleset-validation
```

The package uses Laravel package auto-discovery, so no manual service provider registration is required.

## If You Already Know Form Requests

For request-backed validation, this package intentionally follows Laravel's `FormRequest` validation model as closely as possible.

Read the Laravel documentation for the validation lifecycle, authorization, hooks, validated input, redirects, error bags, and Precognition:

[Laravel Form Request Validation](https://laravel.com/docs/13.x/validation#form-request-validation)

[Laravel Working With Validated Input](https://laravel.com/docs/13.x/validation#working-with-validated-input)

If you know how to write a `FormRequest`, you already know how to write a `Ruleset`.

## What This Package Adds

The main difference from `FormRequest` is not the validation API, but where the validation logic can live.

### 1. You can attach a ruleset to any object

Create a class that implements `ValidatesWithRuleset`, add the `HasRuleset` trait, then point it at a ruleset.

```php
<?php

namespace App\Data;

use App\Rulesets\CreatePostRuleset;
use Craft\RulesetValidation\Attributes\Ruleset;
use Craft\RulesetValidation\Concerns\HasRuleset;
use Craft\RulesetValidation\Contracts\ValidatesWithRuleset;

#[Ruleset(CreatePostRuleset::class)]
class CreatePostData implements ValidatesWithRuleset
{
    use HasRuleset;

    public function __construct(
        public string $title,
        public ?string $slug = null,
        public ?string $body = null,
    ) {}

    public function validationData(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
        ];
    }
}
```

Then define the ruleset:

```php
<?php

namespace App\Rulesets;

use Craft\RulesetValidation\Ruleset;

class CreatePostRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ];
    }
}
```

Validate the object anywhere in your application:

```php
$data = new CreatePostData(
    title: 'Rulesets are nice',
    slug: 'rulesets-are-nice',
    body: '...',
);

$validated = $data->ruleset->validate();
```

You can also validate only a subset of attributes:

```php
$validated = $data->ruleset
    ->only(['title', 'slug'])
    ->validate();
```

And if you need a non-throwing flow for object-backed validation:

```php
$ruleset = $data->ruleset->only(['title']);

if ($ruleset->fails()) {
    $validator = $ruleset->getValidator();

    // Inspect $validator->errors()
}
```

### 2. You can inject a ruleset directly for a request

If you want `FormRequest` behavior without a `FormRequest` subclass, type-hint the ruleset in your controller action and let Laravel resolve it.

```php
<?php

namespace App\Rulesets;

use Craft\RulesetValidation\Ruleset;

class StorePostRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ];
    }
}
```

```php
use App\Rulesets\StorePostRuleset;

class PostController
{
    public function store(StorePostRuleset $ruleset)
    {
        $validated = $ruleset->validate();

        // ...
    }
}
```

If you need to construct one manually, you can instantiate the ruleset directly:

```php
$ruleset = new StorePostRuleset(subject: $request);
```

### 3. You can reuse one ruleset across multiple contexts via scenarios

Rulesets include a lightweight scenario API that `FormRequest` does not provide.

```php
use Illuminate\Validation\Rule;

class PostRuleset extends Ruleset
{
    public const SCENARIO_DRAFT = 'draft';
    
    public function rules(): array
    {
        return [
            'title' => [
                Rule::requiredIf($this->inScenarios(self::SCENARIO_DRAFT)),
                'string',
                'max:255'
            ],
        ];
    }
}
```

```php
$validated = $data->ruleset
    ->useScenario(PostRuleset::SCENARIO_DRAFT)
    ->validate();
```

Available helpers:

- `useScenario(string $scenario): static`
- `getScenario(): string`
- `inScenarios(string ...$scenarios): bool`

When injecting a ruleset into a controller, you can also set the scenario with a parameter attribute:

```php
use App\Rulesets\PostRuleset;
use Craft\RulesetValidation\Attributes\Scenario;

class PostController
{
    public function storeDraft(#[Scenario(PostRuleset::SCENARIO_DRAFT)] PostRuleset $ruleset)
    {
        $validated = $ruleset->validate();

        // ...
    }
}
```

## Form Request Compatibility Notes

When a ruleset is validating an `Illuminate\Http\Request`, the Laravel docs above apply directly to the following features:

- `rules()`
- `authorize()`
- `messages()`
- `attributes()`
- `prepareForValidation()`
- `withValidator()`
- `after()`
- `validated()`
- `safe()`
- `#[StopOnFirstFailure]`
- `#[RedirectTo]`
- `#[RedirectToRoute]`
- `#[ErrorBag]`
- Precognition support

This package mirrors that behavior, with a few small differences:

- `validate()` returns the validated payload directly, like `$request->validate(...)`.
- `only(...)` returns a scoped clone that validates only a subset of attributes, such as `$ruleset->only('title')->validate()`.
- `passes()`, `fails()`, and `getValidator()` are available when you want a non-throwing validation flow.
- Request-backed rulesets may be injected directly into controller actions.
- For object-backed validation, data comes from `validationData()` instead of request input.
- Rulesets can be selected with either the `#[Ruleset(...)]` attribute or a `ruleset(): string` method on the validatable object.

## Choosing A Ruleset For An Object

You can associate a validatable object with a ruleset in two ways.

### Using the `#[Ruleset]` attribute

```php
use App\Rulesets\CreatePostRuleset;
use Craft\RulesetValidation\Attributes\Ruleset;

#[Ruleset(CreatePostRuleset::class)]
class CreatePostData implements ValidatesWithRuleset
{
    // ...
}
```

### Using a `ruleset()` method

```php
use App\Rulesets\AdminPostRuleset;
use App\Rulesets\CreatePostRuleset;
use Craft\RulesetValidation\Concerns\HasRuleset;
use Craft\RulesetValidation\Contracts\ValidatesWithRuleset;

class CreatePostData implements ValidatesWithRuleset
{
    use HasRuleset;

    public function __construct(
        public bool $isAdmin,
        public string $title,
    ) {}

    public function validationData(): array
    {
        return [
            'title' => $this->title,
        ];
    }

    public function ruleset(): string
    {
        return $this->isAdmin
            ? AdminPostRuleset::class
            : CreatePostRuleset::class;
    }
}
```

## Testing

```bash
composer test
```

```bash
composer analyse
```

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Pixel & Tonic](https://github.com/craftcms)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
