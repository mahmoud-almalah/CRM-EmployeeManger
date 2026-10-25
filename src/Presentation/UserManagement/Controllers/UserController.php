<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\User\Commands\CreateUserCommand;
use Application\User\Commands\DeleteUserCommand;
use Application\User\Commands\UpdateUserCommand;
use Application\User\Queries\GetUserQuery;
use Domain\User\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\UserManagement\Requests\CreateUserRequest;
use Presentation\UserManagement\Requests\ListUsersRequest;
use Presentation\UserManagement\Requests\UpdateUserRequest;
use Presentation\UserManagement\Resources\UserResource;

class UserController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListUsersRequest $request): AnonymousResourceCollection
    {
        return UserResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(Request $request, string $id): UserResource
    {
        $include = $request->query('include');

        /** @var User $user */
        $user = $this->queryBus->ask(new GetUserQuery($id, is_string($include) ? explode(',', $include) : []));

        return new UserResource($user);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateUserCommand($request->toData(), $request->role()));

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, string $id): UserResource
    {
        /** @var User $user */
        $user = $this->commandBus->dispatch(new UpdateUserCommand($id, $request->toData(), $request->newRole()));

        return new UserResource($user);
    }

    public function destroy(Request $request, string $id): Response
    {
        $this->commandBus->dispatch(new DeleteUserCommand($id, (string) $request->user()->getAuthIdentifier()));

        return response()->noContent();
    }
}
