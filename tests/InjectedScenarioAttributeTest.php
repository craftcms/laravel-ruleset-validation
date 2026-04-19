<?php

declare(strict_types=1);

it('sets the scenario on an injected ruleset via a parameter attribute', function () {
    $this->postJson('/posts/draft', [])
        ->assertOk()
        ->assertJson([
            'scenario' => 'draft',
            'validated' => [],
        ]);
});
