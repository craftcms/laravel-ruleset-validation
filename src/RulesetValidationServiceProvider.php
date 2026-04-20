<?php

namespace Craft\RulesetValidation;

use Craft\RulesetValidation\Attributes\Scenario;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class RulesetValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->afterResolvingAttribute(Scenario::class, function (Scenario $attribute, mixed $resolved): void {
            if (! $resolved instanceof Ruleset) {
                throw new InvalidArgumentException(sprintf(
                    'The #[%s] attribute may only be used on %s parameters.',
                    Scenario::class,
                    Ruleset::class,
                ));
            }

            $resolved->useScenario($attribute->name);
        });
    }
}
