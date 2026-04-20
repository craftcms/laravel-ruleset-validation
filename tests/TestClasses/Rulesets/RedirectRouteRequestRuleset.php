<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Foundation\Http\Attributes\RedirectToRoute;

#[RedirectToRoute('posts.create')]
class RedirectRouteRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
        ];
    }
}
