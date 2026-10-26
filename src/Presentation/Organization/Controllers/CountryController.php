<?php

declare(strict_types=1);

namespace Presentation\Organization\Controllers;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Country\Commands\CreateCountryCommand;
use Application\Country\Commands\DeleteCountryCommand;
use Application\Country\Commands\UpdateCountryCommand;
use Application\Country\Queries\GetCountryQuery;
use Domain\Country\Entities\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Presentation\Controller;
use Presentation\Organization\Requests\CountryRequest;
use Presentation\Organization\Requests\ListCountriesRequest;
use Presentation\Organization\Resources\CountryResource;

class CountryController extends Controller
{
    public function __construct(
        protected QueryBusContract $queryBus,
        protected CommandBusContract $commandBus,
    ) {}

    public function index(ListCountriesRequest $request): AnonymousResourceCollection
    {
        return CountryResource::collection($this->queryBus->ask($request->toQuery()));
    }

    public function show(string $id): CountryResource
    {
        /** @var Country $country */
        $country = $this->queryBus->ask(new GetCountryQuery($id));

        return CountryResource::make($country);
    }

    public function store(CountryRequest $request): JsonResponse
    {
        /** @var Country $country */
        $country = $this->commandBus->dispatch(new CreateCountryCommand($request->toData()));

        return CountryResource::make($country)->response()->setStatusCode(201);
    }

    public function update(CountryRequest $request, string $id): CountryResource
    {
        /** @var Country $country */
        $country = $this->commandBus->dispatch(new UpdateCountryCommand($id, $request->toData()));

        return CountryResource::make($country);
    }

    public function destroy(string $id): Response
    {
        $this->commandBus->dispatch(new DeleteCountryCommand($id));

        return response()->noContent();
    }
}
