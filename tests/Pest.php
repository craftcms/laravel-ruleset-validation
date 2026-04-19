<?php

use CraftCms\RulesetValidation\Ruleset;
use CraftCms\RulesetValidation\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

afterEach(function () {
    Ruleset::flushState();
});
