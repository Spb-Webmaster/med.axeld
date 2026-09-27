<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setting extends Model
{
    protected $fillable = ['group', 'data'];

    protected $casts = [
        'data' => 'array',
    ];

    public static function getGroup(string $group): self
    {
        return static::firstOrCreate(['group' => $group], ['data' => []]);
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }
}
