<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\Position\Data\PositionData;
use Domain\Position\Entities\Position;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Used for both create (POST) and update (PUT); an update may keep its own name.
 */
final class PositionRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('positions', 'name')->ignore($this->route('id'))],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * A missing position is a 404, whatever else is wrong with the payload.
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('id') !== null && ! Position::query()->whereKey($this->route('id'))->exists()) {
            throw (new ModelNotFoundException)->setModel(Position::class, [(string) $this->route('id')]);
        }
    }

    public function toData(): PositionData
    {
        return PositionData::fromArray($this->validated());
    }
}
