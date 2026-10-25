<?php

namespace Application\User\Data;

use Application\Data\DataEntity;
use Application\User\ValueObjects\EmailObject;
use Application\User\ValueObjects\NameObject;
use Application\User\ValueObjects\PhoneObject;
use DateTimeInterface;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\UserStatus;

final class UserData extends DataEntity
{
    public function __construct(
        protected ?string $id,
        protected NameObject $name,
        protected string $username,
        protected EmailObject $email,
        protected PhoneObject $phone,
        protected ?string $address,
        protected int $country_id,
        protected int $city_id,
        protected DateTimeInterface $birth_date,
        protected DateTimeInterface $start_working_date,
        protected ?DateTimeInterface $end_working_date,
        protected ?string $termination_reason,
        protected int $position_id,
        protected int $work_area_id,
        protected int $work_shift_id,
        protected float $salary,
        protected CurrencyEnum $salary_currency,
        protected bool $is_freelance,
        protected bool $is_remote,
        protected UserStatus $status,
        protected int $role,
        protected ?string $image,
        protected string $password,
        protected ?DateTimeInterface $password_changed_at = null,
        protected ?array $additional_info = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => (string) $this->name,
            'username' => $this->username,
            'email' => (string) $this->email,
            'phone' => (string) $this->phone,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'birth_date' => $this->birth_date,
            'start_working_date' => $this->start_working_date,
            'end_working_date' => $this->end_working_date,
            'termination_reason' => $this->termination_reason,
            'position_id' => $this->position_id,
            'work_area_id' => $this->work_area_id,
            'work_shift_id' => $this->work_shift_id,
            'salary' => $this->salary,
            'salary_currency' => $this->salary_currency,
            'is_freelance' => $this->is_freelance,
            'is_remote' => $this->is_remote,
            'status' => $this->status,
            'role' => $this->role,
            'image' => $this->image,
            'password' => $this->password,
            'password_changed_at' => $this->password_changed_at,
            'additional_info' => $this->additional_info,
        ];
    }
}
