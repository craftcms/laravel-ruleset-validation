<?php

declare(strict_types=1);

namespace CraftCms\RulesetValidation\Concerns;

trait HasScenarios
{
    private string $scenario = 'default';

    public function setScenario(string $scenario): static
    {
        $this->scenario = $scenario;

        return $this;
    }

    public function getScenario(): string
    {
        return $this->scenario;
    }

    public function inScenarios(string ...$scenarios): bool
    {
        return in_array($this->scenario, $scenarios, true);
    }
}
