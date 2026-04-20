<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Concerns;

use Craft\RulesetValidation\Attributes\Ruleset as RulesetAttribute;
use Craft\RulesetValidation\Ruleset;
use Illuminate\Support\Traits\ReadsClassAttributes;

/** @phpstan-ignore trait.unused */
trait HasRuleset
{
    use ReadsClassAttributes;

    public Ruleset|false $ruleset {
        get {
            if (isset($this->ruleset)) {
                return $this->ruleset;
            }

            $class = $this->getAttributeValue($this, RulesetAttribute::class, 'rulesetClass');

            if ($class === null && method_exists($this, 'ruleset')) {
                $class = $this->ruleset();
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
}
