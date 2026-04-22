<?php

declare(strict_types=1);

use CraftCms\RulesetValidation\Tests\TestClasses\Validatables\ScenarioValidatable;

it('tracks the active scenario', function () {
    $ruleset = (new ScenarioValidatable)->ruleset;

    expect($ruleset->getScenario())->toBe('default')
        ->and($ruleset->inScenarios('default'))->toBeTrue();

    $ruleset->useScenario('none');

    expect($ruleset->getScenario())->toBe('none')
        ->and($ruleset->inScenarios('none'))->toBeTrue();
});

it('can validate using a scenario', function () {
    $ruleset = (new ScenarioValidatable)->ruleset;

    expect($ruleset->useScenario('none')->validate())->toBe([]);
});

it('rebuilds the validator when the scenario changes', function () {
    $ruleset = (new ScenarioValidatable)->ruleset;

    expect($ruleset->fails())->toBeTrue()
        ->and($ruleset->useScenario('none')->validate())->toBe([]);
});

it('temporarily uses a scenario within a callback and restores the previous scenario', function () {
    $ruleset = (new ScenarioValidatable)->ruleset->useScenario('none');

    $fails = $ruleset->withScenario('default', fn () => $ruleset->fails());

    expect($fails)->toBeTrue()
        ->and($ruleset->getScenario())->toBe('none')
        ->and($ruleset->validate())->toBe([]);
});

it('restores the previous scenario when the callback throws', function () {
    $ruleset = (new ScenarioValidatable)->ruleset->useScenario('none');

    expect(function () use ($ruleset) {
        $ruleset->withScenario('default', function (): void {
            throw new \RuntimeException('Boom');
        });
    })->toThrow(\RuntimeException::class);

    expect($ruleset->getScenario())->toBe('none')
        ->and($ruleset->validate())->toBe([]);
});
