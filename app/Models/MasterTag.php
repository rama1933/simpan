<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTag extends Model
{
    use HasFactory;

    protected $table = 'table_master_tags';

    protected $fillable = [
        'name',
        'slug',
        'is_active'
    ];
}
