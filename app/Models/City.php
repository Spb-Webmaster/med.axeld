<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'phone',
        'phone2',
        'phone3',
        'phone4',
        'email',
        'email2',
        'email3',
        'address',
        'desc',
        'coordinates',
        'published',
        'sorting',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'integer',
            'sorting'   => 'integer',
        ];
    }
}
