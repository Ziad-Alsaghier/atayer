<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ZoneScope;

class Notification extends Model
{
    protected $appends = ['data'];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset($value);
    }

    public function getDataAttribute()
    {
        return [
            "title" => $this->title,
            "description" => $this->description,
            "order_id" => "",
            "image" => $this->image,
            "type" => "order_status"
        ];
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }
}
