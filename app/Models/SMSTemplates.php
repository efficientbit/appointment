<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSTemplates extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'content', 'active', 'created_at', 'updated_at'];

    protected $table = 'sms_templates';
}
