<?php

use Illuminate\Support\Facades\Log;

it('generates a request id and returns it in the header', function () {
    $id = $this->getJson('/api/health')->headers->get('X-Request-Id');

    expect($id)->toMatch('/^[0-9a-f-]{36}$/');
});

it('keeps a request id the client sent', function () {
    $this->getJson('/api/health', ['X-Request-Id' => 'trace-abc-12345'])
        ->assertHeader('X-Request-Id', 'trace-abc-12345');
});

it('replaces a request id that is unsafe to log', function () {
    $response = $this->getJson('/api/health', ['X-Request-Id' => "bad value\nwith newline"]);

    expect($response->headers->get('X-Request-Id'))->not->toContain('bad')
        ->and($response->headers->get('X-Request-Id'))->toMatch('/^[0-9a-f-]{36}$/');
});

it('shares the request id with every log line of the request', function () {
    $this->getJson('/api/health', ['X-Request-Id' => 'trace-abc-12345']);

    expect(Log::sharedContext())->toMatchArray(['request_id' => 'trace-abc-12345']);
});

it('puts the request id on error responses too', function () {
    $this->getJson('/api/does-not-exist')
        ->assertNotFound()
        ->assertHeader('X-Request-Id');
});
