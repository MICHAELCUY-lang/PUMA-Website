<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'cabinet_id',
        'division_id',
        'position',
        'status',
        'batch',
        'birthdate',
        'joined_date',
        'left_date',
        'created_at',
        'updated_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
