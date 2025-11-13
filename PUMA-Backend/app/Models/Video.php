<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'cabinet_id',
        'batch',
        'order',
        'access_level',
        'views',
    ];

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
    
}
