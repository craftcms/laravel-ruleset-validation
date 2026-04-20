<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Tests\TestClasses\Rulesets;

use CraftCms\RulesetValidation\Ruleset;
use CraftCms\RulesetValidation\Tests\TestClasses\Controllers\PostController;

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
