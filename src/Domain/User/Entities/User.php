<?php

declare(strict_types=1);

namespace Domain\User\Entities;

use Database\Factories\UserFactory;
use Domain\User\Casts\Email;
use Domain\User\Casts\Name;
use Domain\User\Casts\Phone;
use Domain\User\Traits\UserMutators;
use Domain\User\Traits\UserRelations;
use Domain\User\Traits\UserScopes;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\UserStatus;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property NameObject $name
 * @property string $username
 * @property EmailObject $email
 * @property PhoneObject $phone_number
 * @property UserStatus $status
 * @property CurrencyEnum $salary_currency
 * @property string $password
 * @property bool $is_freelance
 * @property bool $is_remote
 * @property Carbon $birth_date
 * @property Carbon $start_working_date
 * @property Carbon|null $end_working_date
 */
final class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles,
        HasUuids, UserMutators,
        UserRelations, UserScopes;

    public const TABLE = 'users';

    protected $table = self::TABLE;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'country_id',
        'city_id',
        'position_id',
        'work_area_id',
        'work_shift_id',
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
            'password' => 'hashed',
            'additional_info' => 'array',
            'birth_date' => 'date',
            'start_working_date' => 'date',
            'end_working_date' => 'date',
            'is_freelance' => 'boolean',
            'is_remote' => 'boolean',
        ];
    }
}
