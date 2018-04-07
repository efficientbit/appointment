<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locations extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'fdo_name', 'fdo_phone',
        'address', 'google_map', 'city_id', 'active', 'created_at', 'updated_at'
    ];

    protected $table = 'locations';

    /**
     * Get the Locations that owns the City.
     */
    public function city()
    {
        return $this->belongsTo('App\Models\Cities')->withTrashed();
    }

    /**
     * Get the doctors for location.
     */
    public function doctors()
    {
        return $this->hasMany('App\Models\Doctors', 'location_id');
    }

    /**
     * Get the appointments for location.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'location_id');
    }

    /**
     * Get the Location name with City Name.
     */
    public function getFullAddressAttribute($value)
    {
        return ucfirst($this->city->name) . ' - ' . ucfirst($this->name);
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSorted()
    {
        return self::get()->pluck('name','id');
    }
}
