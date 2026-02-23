<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Division extends Model
{
    use HasFactory, LogsActivity;

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
