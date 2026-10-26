<?php

declare(strict_types=1);

namespace Application\Supervisor\Data;

use Application\Data\DataEntity;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\PhoneObject;
use Shared\Enums\SupervisorStatus;

final class SupervisorData extends DataEntity
{
    public function __construct(
        public readonly string $name,
        public readonly EmailObject $email,
        public readonly PhoneObject $phone,
        public readonly SupervisorStatus $status = SupervisorStatus::Active,
        public readonly ?string $password = null,
        public readonly ?string $profilePicture = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: new EmailObject($data['email']),
            phone: new PhoneObject($data['phone']['number'], $data['phone']['country_code']),
            status: isset($data['status']) ? SupervisorStatus::from($data['status']) : SupervisorStatus::Active,
            password: $data['password'] ?? null,
            profilePicture: $data['profile_picture'] ?? null,
        );
    }

    /**
     * A missing password is left out so an update never wipes the existing one.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'status' => $this->status,
            'profile_picture' => $this->profilePicture,
        ];

        if ($this->password !== null) {
            $attributes['password'] = $this->password;
        }

        return $attributes;
    }
}
