<?php

use Craft\RulesetValidation\Ruleset;
use Craft\RulesetValidation\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

afterEach(function () {
    Ruleset::flushState();
});
