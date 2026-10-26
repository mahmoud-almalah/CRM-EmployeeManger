<?php

declare(strict_types=1);

namespace Domain\Supervisor\Entities;

use Database\Factories\SupervisorFactory;
use Domain\User\Casts\Email;
use Domain\User\Casts\Phone;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shared\Enums\SupervisorStatus;

/**
 * An external reviewer. Supervisors have their own table and are not employees.
 *
 * @property string $id
 * @property string $name
 * @property EmailObject $email
 * @property PhoneObject $phone_number
 * @property SupervisorStatus $status
 * @property string $password
 * @property string|null $profile_picture
 */
final class Supervisor extends Model
{
    use HasFactory, HasUuids;

    public const TABLE = 'supervisors';

    protected $table = self::TABLE;

    protected $fillable = ['name', 'email', 'phone_number', 'status', 'password', 'profile_picture'];

    protected $hidden = ['password'];

    protected static function newFactory(): SupervisorFactory
    {
        return SupervisorFactory::new();
    }

    protected function casts(): array
    {
        return [
            'email' => Email::class,
            'phone_number' => Phone::class,
            'status' => SupervisorStatus::class,
            'password' => 'hashed',
        ];
    }
}
