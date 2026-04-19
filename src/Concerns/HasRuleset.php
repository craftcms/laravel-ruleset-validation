<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Concerns;

use CraftCms\RulesetValidation\Attributes\Ruleset as RulesetAttribute;
use CraftCms\RulesetValidation\Ruleset;
use InvalidArgumentException;
use ReflectionClass;

/** @phpstan-ignore trait.unused */
trait HasRuleset
{
    public Ruleset $ruleset {
        get {
            if (isset($this->ruleset)) {
                return $this->ruleset;
            }

            $attributes = new ReflectionClass($this)->getAttributes(RulesetAttribute::class);

            $class = null;
            if (isset($attributes[0])) {
                $class = $attributes[0]->newInstance()->class;
            } elseif (method_exists($this, 'ruleset')) {
                $class = $this->ruleset();
            }

            if (is_null($class)) {
                return $this->ruleset = app()->make(Ruleset::class, ['subject' => $this]);
            }

            if (! is_subclass_of($class, Ruleset::class)) {
                throw new InvalidArgumentException('The rules class must be an instance of '.Ruleset::class);
            }

            return $this->ruleset = app()->make($class, ['subject' => $this]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function validationData(): array;
}
