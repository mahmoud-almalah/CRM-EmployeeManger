<?php

namespace Presentation\UserManagement\Controllers;

use Application\Bus\Contracts\QueryBusContract;
use Application\User\Contracts\UserServiceContract;
use Presentation\Controller;
use Application\User\Queries\GetAllUsersQuery;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceContract $userService,
        protected QueryBusContract $queryBus,
    )
    {
    }

    public function index()
    {
        return $this->queryBus->ask(new GetAllUsersQuery());
    }

    public function show(string $id)
    {
        return $this->userService->find($id);
    }
}
