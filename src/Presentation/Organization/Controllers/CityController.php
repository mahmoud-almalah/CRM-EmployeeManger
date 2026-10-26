<?php

declare(strict_types=1);

namespace Presentation\Organization\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\City\Commands\CreateCityCommand;
use Application\City\Commands\DeleteCityCommand;
use Application\City\Commands\UpdateCityCommand;
use Application\City\Queries\GetCityQuery;
use Domain\City\Entities\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\Organization\Requests\CityRequest;
use Presentation\Organization\Requests\ListCitiesRequest;
use Presentation\Organization\Resources\CityResource;

class CityController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListCitiesRequest $request): AnonymousResourceCollection
    {
        return CityResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(Request $request, string $id): CityResource
    {
        $include = $request->query('include');

        /** @var City $city */
        $city = $this->queryBus->ask(new GetCityQuery($id, is_string($include) ? explode(',', $include) : []));

        return CityResource::make($city);
    }

    public function store(CityRequest $request): JsonResponse
    {
        $include = $request->query('include');

        /** @var City $city */
        $city = $this->commandBus->dispatch(new CreateCityCommand($request->toData()));

        return CityResource::make($city)->response()->setStatusCode(201);
    }

    public function update(CityRequest $request, string $id): CityResource
    {
        $include = $request->query('include');

        /** @var City $city */
        $city = $this->commandBus->dispatch(new UpdateCityCommand($id, $request->toData()));

        return CityResource::make($city);
    }

    public function destroy(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteCityCommand($id));

        return response()->noContent();
    }
}
