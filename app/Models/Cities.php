<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cities extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'active', 'is_featured', 'created_at', 'updated_at','sort_number'];

    protected $table = 'cities';

    /**
     * Get the Locations for City.
     */
    public function locations()
    {
        return $this->hasMany('App\Models\Locations', 'city_id');
    }

    /**
     * Get the doctors for City.
     */
    public function doctors()
    {
        return $this->hasMany('App\Models\Doctors', 'city_id');
    }

    /**
     * Get the appointments for City.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'city_id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSorted()
    {
        return self::get()->pluck('name','id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSortedFeatured()
    {
        return self::where(['is_featured' => 1])->get()->pluck('name','id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveOnly()
    {
        return self::where(['active' => 1])->OrderBy('sort_number','asc')->get();
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveFeaturedOnly()
    {
        return self::where(['active' => 1, 'is_featured' => 1])->OrderBy('sort_number','asc')->get();
    }
}
