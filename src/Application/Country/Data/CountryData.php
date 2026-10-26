<?php

declare(strict_types=1);

namespace Application\Country\Data;

use Application\Data\DataEntity;
use Shared\Enums\CurrencyEnum;

final class CountryData extends DataEntity
{
    public function __construct(
        public readonly string $name,
        public readonly string $phoneCode,
        public readonly ?CurrencyEnum $currency = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            phoneCode: $data['phone_code'],
            currency: isset($data['currency']) ? CurrencyEnum::from($data['currency']) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone_code' => $this->phoneCode,
            'currency' => $this->currency,
        ];
    }
}
