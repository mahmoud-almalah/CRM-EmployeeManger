<?php

declare(strict_types=1);

namespace Presentation\Auth\Controllers;

use Application\Auth\Commands\LoginCommand;
use Application\Auth\Commands\LogoutCommand;
use Application\Auth\Data\LoginResult;
use Application\Bus\Contracts\CommandBusContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Presentation\Auth\Requests\LoginRequest;
use Presentation\Controller;
use Presentation\UserManagement\Resources\UserResource;

final class AuthController extends Controller
{
    public function __construct(
        private readonly CommandBusContract $commands,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        /** @var LoginResult $result */
        $result = $this->commands->dispatch(
            new LoginCommand($request->string('login')->toString(), $request->string('password')->toString())
        );

        return response()->json([
            'token' => $result->token,
            'token_type' => 'Bearer',
            'user' => new UserResource($result->user),
        ]);
    }

    public function logout(Request $request): Response
    {
        $this->commands->dispatch(new LogoutCommand($request->user()));

        return response()->noContent();
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
