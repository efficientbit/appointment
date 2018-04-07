<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSources extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'sort_no', 'active', 'created_at', 'updated_at'];

    protected $table = 'lead_sources';

    /**
     * Get the Leads for Lead Source.
     */
    public function leads()
    {
        return $this->hasMany('App\Models\Leads', 'lead_source_id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSorted()
    {
        return self::OrderBy('sort_no','asc')->get()->pluck('name','id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveOnly()
    {
        return self::where(['active' => 1])->OrderBy('sort_no','asc')->get();
    }
}
