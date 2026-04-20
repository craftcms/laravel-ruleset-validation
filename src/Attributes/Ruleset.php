<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Attributes;

use Attribute;
use CraftCms\RulesetValidation\Contracts\ValidatesWithRuleset;
use CraftCms\RulesetValidation\Ruleset as BaseRuleset;

#[Attribute(Attribute::TARGET_CLASS)]
class Ruleset
{
    public function __construct(
        /** @var class-string<BaseRuleset<ValidatesWithRuleset>> */
        public string $class,
    ) {}
}
