<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_id',
        'original_name',
        'path',
        'mime_type',
        'size_bytes',
        'disk',
    ];

    public function knowledge()
    {
        return $this->belongsTo(Knowledge::class);
    }
}
