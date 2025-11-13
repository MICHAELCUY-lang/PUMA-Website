<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /** @use HasFactory<\Database\Factories\DivisionFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'title',
        'description',
        'image',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function cabinets()
    {
        return $this->belongsToMany(Cabinet::class, 'cabinet_divisions', 'division_id', 'cabinet_id')
            ->withPivot('order')
            ->orderBy('cabinet_divisions.order');
    }
}
