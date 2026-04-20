<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Attributes;

use Attribute;
use Craft\RulesetValidation\Contracts\ValidatesWithRuleset;
use Craft\RulesetValidation\Ruleset as BaseRuleset;

#[Attribute(Attribute::TARGET_CLASS)]
class Ruleset
{
    public function __construct(
        /** @var class-string<BaseRuleset<ValidatesWithRuleset>> */
        public string $class,
    ) {}
}
