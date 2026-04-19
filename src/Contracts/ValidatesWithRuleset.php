<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Contracts;

use CraftCms\RulesetValidation\Ruleset;

interface ValidatesWithRuleset
{
    /** @var Ruleset<static> */
    public Ruleset $ruleset { get; }

    /**
     * @return array<string, mixed>
     */
    public function validationData(): array;
}
