<?php

declare(strict_types=1);

namespace Presentation\Organization\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\WorkArea\Commands\CreateWorkAreaCommand;
use Application\WorkArea\Commands\DeleteWorkAreaCommand;
use Application\WorkArea\Commands\UpdateWorkAreaCommand;
use Application\WorkArea\Queries\GetWorkAreaQuery;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\Organization\Requests\ListWorkAreasRequest;
use Presentation\Organization\Requests\WorkAreaRequest;
use Presentation\Organization\Resources\WorkAreaResource;

class WorkAreaController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListWorkAreasRequest $request): AnonymousResourceCollection
    {
        return WorkAreaResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(Request $request, string $id): WorkAreaResource
    {
        $include = $request->query('include');

        /** @var WorkArea $workArea */
        $workArea = $this->queryBus->ask(new GetWorkAreaQuery($id, is_string($include) ? explode(',', $include) : []));

        return WorkAreaResource::make($workArea);
    }

    public function store(WorkAreaRequest $request): JsonResponse
    {
        $include = $request->query('include');

        /** @var WorkArea $workArea */
        $workArea = $this->commandBus->dispatch(new CreateWorkAreaCommand($request->toData()));

        return WorkAreaResource::make($workArea)->response()->setStatusCode(201);
    }

    public function update(WorkAreaRequest $request, string $id): WorkAreaResource
    {
        $include = $request->query('include');

        /** @var WorkArea $workArea */
        $workArea = $this->commandBus->dispatch(new UpdateWorkAreaCommand($id, $request->toData()));

        return WorkAreaResource::make($workArea);
    }

    public function destroy(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteWorkAreaCommand($id));

        return response()->noContent();
    }
}
