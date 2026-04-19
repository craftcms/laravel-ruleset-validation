<?php

declare(strict_types=1);

it('can inject a request backed ruleset into a controller action', function () {
    $this->postJson('/posts', [
        'title' => 'Injected title',
    ])->assertOk()->assertJson([
        'title' => 'Injected title',
    ]);
});

it('resolves the request subject before lifecycle hooks run on injected rulesets', function () {
    $this->postJson('/posts/hooked', [
        'title' => '  Injected title  ',
    ])->assertOk()->assertJson([
        'title' => 'Injected title',
    ]);
});
