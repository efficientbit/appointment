<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatments extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'active', 'created_at', 'updated_at'];

    protected $table = 'treatments';

    /**
     * Get the Appointments for Treatment.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'treatment_id');
    }
}
