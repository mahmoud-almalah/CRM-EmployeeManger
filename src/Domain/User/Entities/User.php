<?php

declare(strict_types=1);

namespace Domain\User\Entities;

use Domain\User\Casts\Email;
use Domain\User\Casts\Name;
use Domain\User\Casts\Phone;
use Domain\User\Traits\UserMutators;
use Domain\User\Traits\UserRelations;
use Domain\User\Traits\UserScopes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\UserStatus;
use Spatie\Permission\Traits\HasRoles;

final class User extends Authenticatable
{
    use HasApiTokens, UserScopes,
        UserRelations, UserMutators,
        HasUuids, HasRoles;

    public const TABLE = 'users';

    protected $table = self::TABLE;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'address',
        'birth_date',
        'start_working_date',
        'end_working_date',
        'termination_reason',
        'salary',
        'salary_currency',
        'is_freelance',
        'is_remote',
        'status',
        'role',
        'image',
        'password',
        'password_changed_at',
        'remember_token',
        'additional_info',
    ];

    protected function casts(): array
    {
        return [
            'name' => Name::class,
            'email' => Email::class,
            'phone_number' => Phone::class,
            'status' => UserStatus::class,
            'salary_currency' => CurrencyEnum::class,
            'additional_info' => 'array',
            'birth_date' => 'date',
            'start_working_date' => 'date',
            'end_working_date' => 'date',
            'is_freelance' => 'boolean',
            'is_remote' => 'boolean',
        ];
    }
}
