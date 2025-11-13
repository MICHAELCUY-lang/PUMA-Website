<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_date_end',
        'location',
        'cabinet_id',
        'status',
        'content',
        'category',
        'views',
    ];

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function event_images()
    {
        return $this->hasMany(EventImage::class);
    }
    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

}
