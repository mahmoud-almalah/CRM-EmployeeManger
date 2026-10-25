<?php

declare(strict_types=1);

namespace Application\User\Data;

use Application\Data\DataEntity;
use DateTimeInterface;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Support\Carbon;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\UserStatus;

final class UserData extends DataEntity
{
    public function __construct(
        public readonly NameObject $name,
        public readonly string $username,
        public readonly EmailObject $email,
        public readonly PhoneObject $phone,
        public readonly int $countryId,
        public readonly int $cityId,
        public readonly DateTimeInterface $birthDate,
        public readonly DateTimeInterface $startWorkingDate,
        public readonly int $positionId,
        public readonly int $workAreaId,
        public readonly int $workShiftId,
        public readonly float $salary,
        public readonly ?string $address = null,
        public readonly ?DateTimeInterface $endWorkingDate = null,
        public readonly ?string $terminationReason = null,
        public readonly CurrencyEnum $salaryCurrency = CurrencyEnum::USD,
        public readonly bool $isFreelance = false,
        public readonly bool $isRemote = false,
        public readonly UserStatus $status = UserStatus::Active,
        public readonly ?string $image = null,
        public readonly ?string $password = null,
        public readonly ?array $additionalInfo = null,
    ) {}

    /**
     * Builds the data object from validated request input.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: NameObject::fromArray($data['name']),
            username: $data['username'],
            email: new EmailObject($data['email']),
            phone: new PhoneObject($data['phone']['number'], $data['phone']['country_code']),
            countryId: (int) $data['country_id'],
            cityId: (int) $data['city_id'],
            birthDate: Carbon::parse($data['birth_date']),
            startWorkingDate: Carbon::parse($data['start_working_date']),
            positionId: (int) $data['position_id'],
            workAreaId: (int) $data['work_area_id'],
            workShiftId: (int) $data['work_shift_id'],
            salary: (float) $data['salary'],
            address: $data['address'] ?? null,
            endWorkingDate: isset($data['end_working_date']) ? Carbon::parse($data['end_working_date']) : null,
            terminationReason: $data['termination_reason'] ?? null,
            salaryCurrency: isset($data['salary_currency']) ? CurrencyEnum::from($data['salary_currency']) : CurrencyEnum::USD,
            isFreelance: (bool) ($data['is_freelance'] ?? false),
            isRemote: (bool) ($data['is_remote'] ?? false),
            status: isset($data['status']) ? UserStatus::from($data['status']) : UserStatus::Active,
            image: $data['image'] ?? null,
            password: $data['password'] ?? null,
            additionalInfo: $data['additional_info'] ?? null,
        );
    }

    /**
     * Attributes for the User model. Value objects and enums are passed as
     * objects because the model casts know how to store them. A missing
     * password is left out so an update never wipes the existing one.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'address' => $this->address,
            'country_id' => $this->countryId,
            'city_id' => $this->cityId,
            'birth_date' => $this->birthDate,
            'start_working_date' => $this->startWorkingDate,
            'end_working_date' => $this->endWorkingDate,
            'termination_reason' => $this->terminationReason,
            'position_id' => $this->positionId,
            'work_area_id' => $this->workAreaId,
            'work_shift_id' => $this->workShiftId,
            'salary' => $this->salary,
            'salary_currency' => $this->salaryCurrency,
            'is_freelance' => $this->isFreelance,
            'is_remote' => $this->isRemote,
            'status' => $this->status,
            'image' => $this->image,
            'additional_info' => $this->additionalInfo,
        ];

        if ($this->password !== null) {
            $attributes['password'] = $this->password;
        }

        return $attributes;
    }
}
