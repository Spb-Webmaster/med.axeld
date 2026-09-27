<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Замер давления и пульса за один день.
 *
 * Запись создаёт и правит сам пользователь в календаре
 * (App\Http\Controllers\Cabinet\BloodPressureController).
 * На день приходится не больше одного замера — этим занимается
 * уникальный ключ (user_id, measured_on).
 */
class BloodPressureReading extends Model
{
    protected $fillable = [
        'user_id',
        'measured_on',
        'systolic',
        'diastolic',
        'pulse',
    ];

    protected function casts(): array
    {
        return [
            // Время суток у замера не храним — в календаре он привязан к дате
            'measured_on' => 'date',
            'systolic'    => 'integer',
            'diastolic'   => 'integer',
            'pulse'       => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Замеры для календаря в том виде, в каком он их ждёт.
     *
     * Дневник на сайте один — владельца (User::owner()), и виден он всем,
     * включая гостей. Править его может только сам владелец, за это отвечают
     * BloodPressureRequest и BloodPressureController.
     *
     * @return list<array{date: string, sys: int, dia: int, pulse: int}>
     */
    public static function forCalendar(): array
    {
        $owner = User::owner();

        if (! $owner) {
            return [];
        }

        return self::query()
            ->forUser($owner->id)
            ->orderBy('measured_on')
            ->get()
            ->map(static fn (self $reading): array => [
                'date'  => $reading->measured_on->format('Y-m-d'),
                'sys'   => $reading->systolic,
                'dia'   => $reading->diastolic,
                'pulse' => $reading->pulse,
            ])
            ->all();
    }

    /** Дата замера в том же виде, в каком её присылает календарь */
    public function getDateKeyAttribute(): string
    {
        return $this->measured_on instanceof Carbon
            ? $this->measured_on->format('Y-m-d')
            : (string) $this->measured_on;
    }
}
