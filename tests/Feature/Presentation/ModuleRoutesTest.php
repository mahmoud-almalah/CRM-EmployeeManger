<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Presentation\Resources\ApiResource;

it('loads the routes of every presentation module', function () {
    $files = glob(base_path('src/Presentation/*/routes/api.php'));
    $uris = collect(Route::getRoutes()->getRoutes())->map->uri()->all();

    expect($files)->not->toBeEmpty()
        ->and($uris)->toContain('api/auth/login')
        ->and($uris)->toContain('api/users');
});

it('wraps a single resource in a data envelope', function () {
    $resource = new class((object) ['id' => 7]) extends ApiResource
    {
        public function toArray(Request $request): array
        {
            return ['id' => $this->resource->id];
        }
    };

    expect($resource->response()->getData(true))->toBe(['data' => ['id' => 7]]);
});

it('formats dates as ISO 8601 and keeps missing ones null', function () {
    $resource = new class((object) ['created_at' => now()->setDate(2026, 1, 2)->setTime(3, 4, 5), 'updated_at' => null]) extends ApiResource
    {
        public function toArray(Request $request): array
        {
            return $this->timestamps();
        }
    };

    $body = $resource->response()->getData(true)['data'];

    expect($body['created_at'])->toStartWith('2026-01-02T03:04:05')
        ->and($body['updated_at'])->toBeNull();
});
