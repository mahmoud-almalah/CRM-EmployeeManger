<?php

declare(strict_types=1);

namespace Presentation\Organization\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\WorkShift\Commands\CreateWorkShiftCommand;
use Application\WorkShift\Commands\DeleteWorkShiftCommand;
use Application\WorkShift\Commands\UpdateWorkShiftCommand;
use Application\WorkShift\Queries\GetWorkShiftQuery;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\Organization\Requests\ListWorkShiftsRequest;
use Presentation\Organization\Requests\WorkShiftRequest;
use Presentation\Organization\Resources\WorkShiftResource;

class WorkShiftController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListWorkShiftsRequest $request, string $workAreaId): AnonymousResourceCollection
    {
        return WorkShiftResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(string $workAreaId, string $id): WorkShiftResource
    {
        /** @var WorkShift $shift */
        $shift = $this->queryBus->ask(new GetWorkShiftQuery($workAreaId, $id));

        return WorkShiftResource::make($shift);
    }

    public function store(WorkShiftRequest $request, string $workAreaId): JsonResponse
    {
        /** @var WorkShift $shift */
        $shift = $this->commandBus->dispatch(new CreateWorkShiftCommand($request->toData()));

        return WorkShiftResource::make($shift)->response()->setStatusCode(201);
    }

    public function update(WorkShiftRequest $request, string $workAreaId, string $id): WorkShiftResource
    {
        /** @var WorkShift $shift */
        $shift = $this->commandBus->dispatch(new UpdateWorkShiftCommand($id, $request->toData()));

        return WorkShiftResource::make($shift);
    }

    public function destroy(string $workAreaId, string $id): Response
    {
        $this->commandBus->dispatch(new DeleteWorkShiftCommand($workAreaId, $id));

        return response()->noContent();
    }
}
