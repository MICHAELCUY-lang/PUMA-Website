<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Member extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'cabinet_id',
        'division_id',
        'name',
        'email',
        'position',
        'status',
        'batch',
        'birthdate',
        'joined_date',
        'left_date',
        'display_order',
        'is_visible',
        'photo_path',
        'instagram_url',
        'linkedin_url',
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
