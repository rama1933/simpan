<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'action'
    ];

    protected $casts = [
        'properties' => 'array'
    ];

    /**
     * Get the subject of the activity
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity
     */
    public function causer()
    {
        return $this->morphTo();
    }
}
