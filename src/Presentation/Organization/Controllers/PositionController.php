<?php

declare(strict_types=1);

namespace Presentation\Organization\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Position\Commands\CreatePositionCommand;
use Application\Position\Commands\DeletePositionCommand;
use Application\Position\Commands\UpdatePositionCommand;
use Application\Position\Queries\GetPositionQuery;
use Domain\Position\Entities\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\Organization\Requests\ListPositionsRequest;
use Presentation\Organization\Requests\PositionRequest;
use Presentation\Organization\Resources\PositionResource;

class PositionController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListPositionsRequest $request): AnonymousResourceCollection
    {
        return PositionResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(string $id): PositionResource
    {
        /** @var Position $position */
        $position = $this->queryBus->ask(new GetPositionQuery($id));

        return PositionResource::make($position);
    }

    public function store(PositionRequest $request): JsonResponse
    {
        /** @var Position $position */
        $position = $this->commandBus->dispatch(new CreatePositionCommand($request->toData()));

        return PositionResource::make($position)->response()->setStatusCode(201);
    }

    public function update(PositionRequest $request, string $id): PositionResource
    {
        /** @var Position $position */
        $position = $this->commandBus->dispatch(new UpdatePositionCommand($id, $request->toData()));

        return PositionResource::make($position);
    }

    public function destroy(string $id): Response
    {
        $this->commandBus->dispatch(new DeletePositionCommand($id));

        return response()->noContent();
    }
}
