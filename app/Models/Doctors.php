<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctors extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'city_id', 'location_id', 'active', 'created_at', 'updated_at'];

    protected $table = 'doctors';

    /**
     * Get the Doctors that owns the City.
     */
    public function city()
    {
        return $this->belongsTo('App\Models\Cities')->withTrashed();
    }

    /**
     * Get the Doctors that owns the Location.
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Locations')->withTrashed();
    }

    /**
     * Get the Appointments for Doctors.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'doctor_id');
    }
}
