<?php

namespace Craft\RulesetValidation\Tests;

use Craft\RulesetValidation\RulesetValidationServiceProvider;
use Craft\RulesetValidation\Tests\TestClasses\Controllers\InjectedHookedRulesetController;
use Craft\RulesetValidation\Tests\TestClasses\Controllers\InjectedRulesetController;
use Craft\RulesetValidation\Tests\TestClasses\Controllers\PostController;
use Craft\RulesetValidation\Tests\TestClasses\Controllers\ScenarioInjectedRulesetController;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            RulesetValidationServiceProvider::class,
        ];
    }

    protected function defineRoutes($router): void
    {
        $router->get('/posts/create', [PostController::class, 'create'])->name('posts.create');
        $router->post('/posts', [InjectedRulesetController::class, 'store']);
        $router->post('/posts/hooked', [InjectedHookedRulesetController::class, 'store']);
        $router->post('/posts/draft', [ScenarioInjectedRulesetController::class, 'storeDraft']);
    }
}
