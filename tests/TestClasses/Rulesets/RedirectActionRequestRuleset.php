<?php

declare(strict_types=1);

namespace Craft\RulesetValidation\Tests\TestClasses\Rulesets;

use Craft\RulesetValidation\Ruleset;
use Craft\RulesetValidation\Tests\TestClasses\Controllers\PostController;

class RedirectActionRequestRuleset extends Ruleset
{
    protected ?string $redirectAction = PostController::class.'@create';

    public function rules(): array
    {
        return [
            'title' => ['required'],
        ];
    }
}
