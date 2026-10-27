<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\Health\Controllers\HealthController;

// Public on purpose: load balancers and uptime monitors call it without a token.
// It reports which dependency failed but never why.
Route::get('health', HealthController::class);
