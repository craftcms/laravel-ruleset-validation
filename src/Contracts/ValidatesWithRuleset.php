<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Contracts;

use Craft\RulesetValidation\Ruleset;

interface ValidatesWithRuleset
{
    /** @var Ruleset<static>|false */
    public Ruleset|false $ruleset { get; }

    /**
     * @return array<string, mixed>
     */
    public function validationData(): array;
}
