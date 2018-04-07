<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentStatuses extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'sort_no', 'active', 'created_at', 'updated_at','sort_no'];

    protected $table = 'appointment_statuses';

    /**
     * Get the Appointments for Appointment Status.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'appointment_status_id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSorted()
    {
        return self::where(['active' => 1])->OrderBy('sort_no','asc')->get()->pluck('name','id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveOnly()
    {
        return self::where(['active' => 1])->OrderBy('sort_no','asc')->get();
    }
}
