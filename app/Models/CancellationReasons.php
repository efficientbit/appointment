<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancellationReasons extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'active', 'created_at', 'updated_at'];

    protected $table = 'cancellation_reasons';

    /**
     * Get the Appointments for Treatment.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'cancellation_reason_id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSorted()
    {
        return self::where(['active' => 1])->OrderBy('sort_no','asc')->get()->pluck('name','id');
    }
}
