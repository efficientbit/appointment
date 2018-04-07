<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatuses extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'sort_no', 'active', 'created_at', 'updated_at','sort_no'];

    protected $table = 'lead_statuses';

    /**
     * Get the Leads for Lead Status.
     */
    public function leads()
    {
        return $this->hasMany('App\Models\Leads', 'lead_status_id');
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveSorted($skip_ids = false, $include_ids = false)
    {
        if($skip_ids && !is_array($skip_ids)) {
            $skip_ids = array($skip_ids);
        }
        if($include_ids && !is_array($include_ids)) {
            $include_ids = array($include_ids);
        }

        if($skip_ids && $include_ids) {
            return self::where(['active' => 1])->whereIn('id', $include_ids)->whereNotIn('id', $skip_ids)->OrderBy('sort_no','asc')->get()->pluck('name','id');
        } else if($skip_ids) {
            return self::where(['active' => 1])->whereNotIn('id', $skip_ids)->OrderBy('sort_no','asc')->get()->pluck('name','id');
        } else if($include_ids) {
            return self::where(['active' => 1])->whereIn('id', $include_ids)->OrderBy('sort_no','asc')->get()->pluck('name','id');
        } else {
            return self::where(['active' => 1])->OrderBy('sort_no','asc')->get()->pluck('name','id');
        }
    }

    /**
     * Get active and sorted data only.
     */
    static public function getActiveOnly()
    {
        return self::where(['active' => 1])->OrderBy('sort_no','asc')->get();
    }
}
