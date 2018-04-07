<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patients extends Model
{
    use SoftDeletes;

    protected $fillable = ['full_name', 'email', 'phone', 'gender', 'active', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected $table = 'patients';

    /**
     * Get the Leads for Patient.
     */
    public function leads()
    {
        return $this->hasMany('App\Models\Leads', 'lead_source_id');
    }

    /**
     * Get the User that owns the Patient.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

}
