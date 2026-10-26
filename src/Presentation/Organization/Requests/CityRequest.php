<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\City\Data\CityData;
use Domain\City\Entities\City;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Used for both create (POST) and update (PUT). A city name only has to be
 * unique inside its country.
 */
final class CityRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('cities', 'name')->where('country_id', $this->input('country_id'))->ignore($this->route('id')),
            ],
            'code' => ['nullable', 'string', 'max:10'],
            'coordinates' => ['nullable', 'array:lat,lng'],
            'coordinates.lat' => ['required_with:coordinates', 'numeric', 'between:-90,90'],
            'coordinates.lng' => ['required_with:coordinates', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'timezone:all'],
        ];
    }

    /**
     * A missing city is a 404, whatever else is wrong with the payload.
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('id') !== null && ! City::query()->whereKey($this->route('id'))->exists()) {
            throw (new ModelNotFoundException)->setModel(City::class, [(string) $this->route('id')]);
        }
    }

    public function toData(): CityData
    {
        return CityData::fromArray($this->validated());
    }
}
