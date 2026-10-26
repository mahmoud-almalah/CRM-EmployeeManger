<?php

declare(strict_types=1);

namespace Presentation\Organization\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Supervisor\Commands\CreateSupervisorCommand;
use Application\Supervisor\Commands\DeleteSupervisorCommand;
use Application\Supervisor\Commands\UpdateSupervisorCommand;
use Application\Supervisor\Queries\GetSupervisorQuery;
use Domain\Supervisor\Entities\Supervisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\Organization\Requests\ListSupervisorsRequest;
use Presentation\Organization\Requests\SupervisorRequest;
use Presentation\Organization\Resources\SupervisorResource;

class SupervisorController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListSupervisorsRequest $request): AnonymousResourceCollection
    {
        return SupervisorResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(string $id): SupervisorResource
    {
        /** @var Supervisor $supervisor */
        $supervisor = $this->queryBus->ask(new GetSupervisorQuery($id));

        return SupervisorResource::make($supervisor);
    }

    public function store(SupervisorRequest $request): JsonResponse
    {
        /** @var Supervisor $supervisor */
        $supervisor = $this->commandBus->dispatch(new CreateSupervisorCommand($request->toData()));

        return SupervisorResource::make($supervisor)->response()->setStatusCode(201);
    }

    public function update(SupervisorRequest $request, string $id): SupervisorResource
    {
        /** @var Supervisor $supervisor */
        $supervisor = $this->commandBus->dispatch(new UpdateSupervisorCommand($id, $request->toData()));

        return SupervisorResource::make($supervisor);
    }

    public function destroy(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteSupervisorCommand($id));

        return response()->noContent();
    }
}
