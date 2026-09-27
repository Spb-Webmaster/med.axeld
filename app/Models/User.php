<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Пользователь сайта: входит в личный кабинет и правит там свои настройки.
 *
 * Это НЕ администратор панели — у MoonShine своя таблица moonshine_users.
 * Регистрации на сайте нет: учётные записи заводит администратор
 * в App\MoonShine\Resources\User\UserResource.
 *
 * Вход — App\Http\Controllers\Auth\LoginController,
 * кабинет — App\Http\Controllers\CabinetController.
 */
#[Fillable(['name', 'email', 'phone', 'birth_date', 'password', 'published'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'published' => 'boolean',

            /*
             | Дату рождения кастуем в 'date', а не 'datetime': время суток у неё
             | смысла не имеет, а Carbon с обнулённым временем корректно
             | сравнивается с now() при проверке возраста.
             */
            'birth_date' => 'date',
        ];
    }

    /** Активные учётные записи: снятая администратором отметка закрывает вход */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    /**
     * Владелец дневника — тот, чьи замеры показывает сайт.
     *
     * Сервис рассчитан на одного человека: регистрации нет, учётная запись
     * заводится администратором в MoonShine. Владельцем считаем самую раннюю
     * запись — если в панели по недосмотру заведут вторую, дневник не подменится,
     * а новый пользователь просто не получит права на запись.
     *
     * once() держит результат в пределах запроса: метод дёргают и шаблон,
     * и контроллер, а в тестах Laravel это кэширование отключает само.
     */
    public static function owner(): ?self
    {
        return once(static fn (): ?self => self::query()->orderBy('id')->first());
    }

    /** Может ли этот пользователь вести дневник */
    public function isDiaryOwner(): bool
    {
        return self::owner()?->is($this) ?? false;
    }

    /** Замеры давления и пульса — по одному на день */
    public function bloodPressureReadings(): HasMany
    {
        return $this->hasMany(BloodPressureReading::class)->orderBy('measured_on');
    }
}
