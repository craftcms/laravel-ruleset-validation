<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Illuminate\Foundation\Http\Attributes\ErrorBag;
use Illuminate\Foundation\Http\Attributes\RedirectTo;

#[RedirectTo('/posts/create')]
#[ErrorBag('createPost')]
class RedirectingRequestRuleset extends Ruleset
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
        ];
    }
}
