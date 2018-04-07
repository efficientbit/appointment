<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'data', 'active', 'created_at', 'updated_at'];

    protected $table = 'settings';
}
