<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class Leads extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id', 'city_id', 'lead_status_id', 'lead_source_id', 'msg_count',
        'active', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];

    protected $table = 'leads';

    /**
     * Get the Patient that owns the Lead.
     */
    public function patient()
    {
        return $this->belongsTo('App\Models\Patients', 'patient_id')->withTrashed();
    }

    /**
     * Get the Lead that owns the City.
     */
    public function city()
    {
        return $this->belongsTo('App\Models\Cities')->withTrashed();
    }

    /**
     * Get the Lead Status that owns the Lead.
     */
    public function lead_status()
    {
        return $this->belongsTo('App\Models\LeadStatuses')->withTrashed();
    }

    /**
     * Get the Leads Source that owns the Lead.
     */
    public function lead_source()
    {
        return $this->belongsTo('App\Models\LeadSources')->withTrashed();
    }

    /**
     * Get the User that owns the Lead.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }

    /**
     * Get the lead comments for lead.
     */
    public function lead_comments()
    {
        return $this->hasMany('App\Models\LeadComments', 'lead_id')->OrderBy('created_at','desc');
    }

    /**
     * Get the lead appointments for lead.
     */
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'lead_id');
    }

    /**
     * Prepare SMS Contnet for Delivery
     *
     * @param: int $lead_id
     * @param: int $smsContent
     *
     * @return: string
     */
    static public function prepareSMSContent($lead_id = false, $smsContent)
    {
        if(!$lead_id) {
            return $smsContent;
        } else {
            // Load Globar Setting for Head Office
            $Setting = Settings::find(5);

            $smsContent = str_replace('##head_office_phone##', $Setting->data, $smsContent);

            $lead = self::find($lead_id);

            if($lead) {
                $Patient = Patients::find($lead->patient_id);

                // Replace Patient Information
                $smsContent = str_replace('##full_name##', $Patient->full_name, $smsContent);
                $smsContent = str_replace('##email##', $Patient->email, $smsContent);
                $smsContent = str_replace('##phone##', $Patient->phone, $smsContent);
                $smsContent = str_replace('##gender##', Config::get('constants.gender_array')[$Patient->gender], $smsContent);

                // Load and Replace City Information
                $Citie = Cities::find($lead->city_id);
                if($Citie) {
                    $smsContent = str_replace('##city_name##', $Citie->name, $smsContent);
                }

                // Load and Replace Lead Source Information
                $LeadSource = LeadSources::find($lead->lead_source_id);
                if($LeadSource) {
                    $smsContent = str_replace('##lead_source_name##', $LeadSource->name, $smsContent);
                }

                // Load and Replace Lead Status Information
                $LeadStatus = LeadStatuses::find($lead->lead_source_id);
                if($LeadStatus) {
                    $smsContent = str_replace('##lead_status_name##', $LeadStatus->name, $smsContent);
                }

            }

            return $smsContent;
        }
    }
}
