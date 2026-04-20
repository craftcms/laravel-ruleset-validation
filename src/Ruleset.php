<?php

declare(strict_types=1);

namespace Craft\RulesetValidation;

use Craft\RulesetValidation\Contracts\ValidatesWithRuleset;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\Attributes\ErrorBag;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\Attributes\RedirectTo;
use Illuminate\Foundation\Http\Attributes\RedirectToRoute;
use Illuminate\Foundation\Http\Attributes\StopOnFirstFailure;
use Illuminate\Foundation\Precognition;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\ValidatedInput;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use ReflectionClass;
use RuntimeException;

/**
 * @template T of ValidatesWithRuleset
 */
class Ruleset
{
    use Conditionable;

    /**
     * The URI to redirect to if validation fails.
     */
    protected ?string $redirect = null;

    /**
     * The route to redirect to if validation fails.
     */
    protected ?string $redirectRoute = null;

    /**
     * The controller action to redirect to if validation fails.
     */
    protected ?string $redirectAction = null;

    /**
     * The key to be used for the view error bag.
     */
    protected string $errorBag = 'default';

    /**
     * Indicates whether validation should stop after the first rule failure.
     */
    protected bool $stopOnFirstFailure = false;

    /**
     * The validator instance.
     */
    protected ?Validator $validator = null;

    /**
     * The active validation scenario.
     */
    protected string $scenario = 'default';

    /**
     * The attributes currently being validated.
     *
     * @var array<string>|null
     */
    protected ?array $validationAttributes = null;

    /**
     * The container instance.
     */
    protected ?Container $container = null;

    /**
     * The redirector instance.
     */
    protected ?Redirector $redirector = null;

    /**
     * Indicates if unknown fields should be rejected for all rulesets.
     */
    protected static bool $globalFailOnUnknownFields = false;

    public function __construct(
        /** @var T|Request|null */
        protected ValidatesWithRuleset|Request|null $subject = null,
    ) {}

    /**
     * Validate the current ruleset and return the validated payload.
     *
     * @return array<string, mixed>
     */
    public function validate(): array
    {
        $this->runValidation(throw: true);

        $instance = $this->getValidator();

        /** @var array<string, mixed> */
        return $instance->validated();
    }

    /**
     * Determine if the current ruleset passes validation without throwing.
     */
    public function passes(): bool
    {
        return $this->runValidation(throw: false);
    }

    /**
     * Determine if the current ruleset fails validation without throwing.
     */
    public function fails(): bool
    {
        return ! $this->passes();
    }

    /**
     * Limit the next validation-related call to the given attributes.
     *
     * @param  array<string>|string  $attributes
     */
    public function only(array|string $attributes): static
    {
        $this->validationAttributes = Arr::wrap($attributes);
        $this->validator = null;

        return $this;
    }

    public function useScenario(string $scenario): static
    {
        $this->scenario = $scenario;
        $this->validator = null;

        return $this;
    }

    public function getScenario(): string
    {
        return $this->scenario;
    }

    public function inScenarios(string ...$scenarios): bool
    {
        return in_array($this->scenario, $scenarios, true);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        //
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        //
    }

    /**
     * Get the validator instance for the ruleset.
     */
    public function getValidator(): Validator
    {
        if ($this->validator) {
            return $this->validator;
        }

        $this->configureFromAttributes();

        $container = $this->resolveContainer();
        $factory = $container->make(ValidationFactory::class);

        if (method_exists($this, 'validator')) {
            $validator = $container->call($this->validator(...), compact('factory'));
        } else {
            $validator = $this->createDefaultValidator($factory);
        }

        if ($this->validationAttributes !== null) {
            $validator->setRules(
                Arr::only($validator->getRulesWithoutPlaceholders(), $this->validationAttributes),
            );
        }

        $subject = $this->resolveSubject();

        if ($subject instanceof Request && $subject->isPrecognitive()) {
            $validator->after(Precognition::afterValidationHook($subject));
        }

        if (method_exists($this, 'withValidator')) {
            $this->withValidator($validator);
        }

        if (method_exists($this, 'after')) {
            $validator->after($container->call(
                $this->after(...),
                ['validator' => $validator],
            ));
        }

        if ($this->shouldFailOnUnknownFields()) {
            $validator->after(function (Validator $validator) {
                $this->validateNoUnknownFields($validator);
            });
        }

        $this->setValidator($validator);

        return $validator;
    }

    /**
     * Configure the ruleset from class attributes.
     */
    protected function configureFromAttributes(): void
    {
        $reflection = new ReflectionClass($this);

        if (count($reflection->getAttributes(StopOnFirstFailure::class)) > 0) {
            $this->stopOnFirstFailure = true;
        }

        $redirectTo = $reflection->getAttributes(RedirectTo::class);

        if (count($redirectTo) > 0) {
            $this->redirect = $redirectTo[0]->newInstance()->url;
        }

        $redirectToRoute = $reflection->getAttributes(RedirectToRoute::class);

        if (count($redirectToRoute) > 0) {
            $this->redirectRoute = $redirectToRoute[0]->newInstance()->route;
        }

        $errorBag = $reflection->getAttributes(ErrorBag::class);

        if (count($errorBag) > 0) {
            $this->errorBag = $errorBag[0]->newInstance()->name;
        }
    }

    /**
     * Create the default validator instance.
     */
    protected function createDefaultValidator(ValidationFactory $factory): Validator
    {
        $rules = $this->validationRules();
        $subject = $this->resolveSubject();

        /** @var Validator $validator */
        $validator = $factory->make(
            $this->validationData(),
            $rules,
            $this->messages(),
            $this->attributes(),
        );

        $validator->stopOnFirstFailure($this->stopOnFirstFailure);

        if ($subject instanceof Request && $subject->isPrecognitive()) {
            $validator->setRules(
                $subject->filterPrecognitiveRules($validator->getRulesWithoutPlaceholders()),
            );
        }

        return $validator;
    }

    /**
     * Get data to be validated from the subject.
     *
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        $subject = $this->resolveSubject();

        if ($subject instanceof Request) {
            return $subject->all();
        }

        return $subject->validationData();
    }

    /**
     * Resolve the current validation subject.
     *
     * @return T|Request
     */
    protected function resolveSubject(): ValidatesWithRuleset|Request
    {
        if ($this->subject) {
            return $this->subject;
        }

        if ($this->resolveContainer()->bound(Request::class)) {
            return $this->subject = $this->resolveContainer()->make(Request::class);
        }

        throw new RuntimeException('No validation subject was provided and no current request could be resolved.');
    }

    /**
     * Get the validation rules for this ruleset.
     *
     * @return array<string, array<Rule|ValidationRule|string>|string>
     */
    protected function validationRules(): array
    {
        return method_exists($this, 'rules') ? $this->resolveContainer()->call([$this, 'rules']) : [];
    }

    /**
     * Determine if fields not present in rules should fail validation.
     */
    protected function shouldFailOnUnknownFields(): bool
    {
        $failOnUnknownFields = new ReflectionClass($this)->getAttributes(FailOnUnknownFields::class);

        return $failOnUnknownFields !== []
            ? $failOnUnknownFields[0]->newInstance()->value
            : static::$globalFailOnUnknownFields;
    }

    /**
     * Validate that no unknown fields were sent as input.
     */
    protected function validateNoUnknownFields(Validator $validator): void
    {
        $subject = $this->resolveSubject();

        if (! $subject instanceof Request) {
            return;
        }

        $allowedKeys = array_keys($this->validationRules());

        foreach (array_keys(Arr::dot($subject->all())) as $inputKey) {
            if ($this->isKnownField($inputKey, $allowedKeys)) {
                continue;
            }

            $validator->errors()->add($inputKey, trans('validation.prohibited', [
                'attribute' => str_replace('_', ' ', $inputKey),
            ]));
        }
    }

    /**
     * Determine if the given input key is an allowed key based on the validation rules.
     *
     * @param  string[]  $allowedKeys
     */
    protected function isKnownField(string $inputKey, array $allowedKeys): bool
    {
        foreach ($allowedKeys as $ruleKey) {
            if ($ruleKey === $inputKey) {
                return true;
            }

            if (str_contains($ruleKey, '*')) {
                $pattern = '/^'.str_replace('\*', '[^.]+', preg_quote($ruleKey, '/')).'$/';

                if (preg_match($pattern, $inputKey)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): never
    {
        /** @var class-string<ValidationException> $exception */
        $exception = $validator->getException();

        throw new $exception($validator)
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    /**
     * Get the URL to redirect to on a validation error.
     */
    protected function getRedirectUrl(): string
    {
        $url = $this->resolveRedirector()->getUrlGenerator();

        if ($this->redirect) {
            return $url->to($this->redirect);
        }

        if ($this->redirectRoute) {
            return $url->route($this->redirectRoute);
        }

        if ($this->redirectAction) {
            return $url->action($this->redirectAction);
        }

        return $url->previous();
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @throws AuthorizationException
     */
    protected function passesAuthorization(): bool
    {
        if (method_exists($this, 'authorize')) {
            $result = $this->resolveContainer()->call([$this, 'authorize']);

            return (bool) ($result instanceof Response ? $result->authorize() : $result);
        }

        return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @throws AuthorizationException
     */
    protected function failedAuthorization(): never
    {
        throw new AuthorizationException;
    }

    /**
     * Get a validated input container for the validated input.
     *
     * @param  array<string>|null  $keys
     * @return ValidatedInput|array<string, mixed>
     */
    public function safe(?array $keys = null): ValidatedInput|array
    {
        return is_array($keys)
            ? $this->getValidator()->safe()->only($keys)
            : $this->getValidator()->safe();
    }

    /**
     * Get the validated data from the request.
     *
     * @param  array<string>|int|string|null  $key
     */
    public function validated(array|int|string|null $key = null, mixed $default = null): mixed
    {
        return data_get($this->getValidator()->validated(), $key, $default);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Enable or disable unknown-field rejection globally for all rulesets.
     */
    public static function failOnUnknownFields(bool $value = true): void
    {
        static::$globalFailOnUnknownFields = $value;
    }

    /**
     * Set the Validator instance.
     */
    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Set the Redirector instance.
     */
    public function setRedirector(Redirector $redirector): static
    {
        $this->redirector = $redirector;

        return $this;
    }

    /**
     * Set the container implementation.
     */
    public function setContainer(Container $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Resolve the container instance.
     */
    protected function resolveContainer(): Container
    {
        return $this->container ??= app();
    }

    /**
     * Resolve the redirector instance.
     */
    protected function resolveRedirector(): Redirector
    {
        return $this->redirector ??= $this->resolveContainer()->make(Redirector::class);
    }

    /**
     * Flush the global state of the ruleset.
     */
    public static function flushState(): void
    {
        static::$globalFailOnUnknownFields = false;
    }

    /**
     * Run the validation lifecycle for the given attributes.
     */
    protected function runValidation(bool $throw = true): bool
    {
        $this->resolveSubject();

        $this->prepareForValidation();

        if (! $this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $instance = $this->getValidator();

        if ($instance->fails()) {
            if ($throw) {
                $this->failedValidation($instance);
            }

            return false;
        }

        $this->passedValidation();

        return true;
    }
}
