<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\Country\Data\CountryData;
use Domain\Country\Entities\Country;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\CurrencyEnum;

/**
 * Used for both create (POST) and update (PUT); an update may keep its own name.
 */
final class CountryRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('countries', 'name')->ignore($this->route('id'))],
            'phone_code' => ['required', 'string', 'regex:/^\+\d{1,4}$/'],
            'currency' => ['nullable', Rule::enum(CurrencyEnum::class)],
        ];
    }

    /**
     * A missing country is a 404, whatever else is wrong with the payload.
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('id') !== null && ! Country::query()->whereKey($this->route('id'))->exists()) {
            throw (new ModelNotFoundException)->setModel(Country::class, [(string) $this->route('id')]);
        }
    }

    public function toData(): CountryData
    {
        return CountryData::fromArray($this->validated());
    }
}
