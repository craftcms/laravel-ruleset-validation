<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Concerns;

use CraftCms\RulesetValidation\Attributes\Ruleset as RulesetAttribute;
use CraftCms\RulesetValidation\Ruleset;
use ReflectionClass;

/** @phpstan-ignore trait.unused */
trait HasRuleset
{
    public Ruleset|false $ruleset {
        get {
            if (isset($this->ruleset)) {
                return $this->ruleset;
            }

            $class = $this->resolveOwnRulesetClass();

            if ($class === null && method_exists($this, 'ruleset')) {
                $class = $this->ruleset();
            }

            if ($class === null) {
                $class = $this->resolveInheritedRulesetClass();
            }

            if (is_null($class)) {
                return $this->ruleset = false;
            }

            if (! is_subclass_of($class, Ruleset::class)) {
                return $this->ruleset = false;
            }

            return $this->ruleset = app()->make($class, ['subject' => $this]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function validationData(): array;

    private function resolveOwnRulesetClass(): ?string
    {
        $attributes = new ReflectionClass($this)->getAttributes(RulesetAttribute::class);

        if (! isset($attributes[0])) {
            return null;
        }

        return $attributes[0]->newInstance()->class;
    }

    private function resolveInheritedRulesetClass(): ?string
    {
        $reflection = new ReflectionClass($this)->getParentClass();

        while ($reflection !== false) {
            $attributes = $reflection->getAttributes(RulesetAttribute::class);

            if (isset($attributes[0])) {
                return $attributes[0]->newInstance()->class;
            }

            $reflection = $reflection->getParentClass();
        }

        return null;
    }
}
