<?php

namespace CraftCms\RulesetValidation\Tests;

use CraftCms\RulesetValidation\RulesetValidationServiceProvider;
use CraftCms\RulesetValidation\Tests\TestClasses\Controllers\InjectedHookedRulesetController;
use CraftCms\RulesetValidation\Tests\TestClasses\Controllers\InjectedRulesetController;
use CraftCms\RulesetValidation\Tests\TestClasses\Controllers\PostController;
use CraftCms\RulesetValidation\Tests\TestClasses\Controllers\ScenarioInjectedRulesetController;
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
