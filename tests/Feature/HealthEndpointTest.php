<?php

it('reports every dependency as ok without a token', function () {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertExactJson([
            'status' => 'ok',
            'checks' => ['database' => 'ok', 'cache' => 'ok', 'queue' => 'ok'],
        ]);
});

it('returns 503 and names the failing check without leaking the cause', function () {
    config(['cache.default' => 'missing-store']);

    $response = $this->getJson('/api/health')->assertStatus(503);

    $response->assertJsonPath('status', 'degraded')
        ->assertJsonPath('checks.cache', 'failed')
        ->assertJsonPath('checks.database', 'ok');

    expect($response->getContent())->not->toContain('missing-store');
});
