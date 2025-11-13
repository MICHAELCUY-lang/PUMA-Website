<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabinet extends Model
{
    /** @use HasFactory<\Database\Factories\CabinetFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'theme_color',
        'year',
        'status',
    ];

    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'cabinet_divisions', 'cabinet_id', 'division_id')
            ->withPivot('order')
            ->orderBy('cabinet_divisions.order');
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
