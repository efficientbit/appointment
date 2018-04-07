<?php

namespace App\Models;

use App\Helpers\GeneralFunctions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointments extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'scheduled_date', 'scheduled_time', 'active',
        'created_by', 'updated_by', 'msg_count', 'lead_id', 'patient_id',
        'appointment_status_id', 'treatment_id', 'cancellation_reason_id', 'reason',
        'doctor_id', 'city_id', 'location_id', 'created_at', 'updated_at'
    ];

    protected $table = 'appointments';

    /**
     * Get the Service that owns the Appointment.
     */
    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatments')->withTrashed();
    }

    /**
     * Get the Appointment Status that owns the Appointment.
     */
    public function appointment_status()
    {
        return $this->belongsTo('App\Models\AppointmentStatuses')->withTrashed();
    }

    /**
     * Get the Appointment Status that owns the Appointment.
     */
    public function cancellation_reason()
    {
        return $this->belongsTo('App\Models\CancellationReasons')->withTrashed();
    }

    /**
     * Get the Doctors that owns the Appointment.
     */
    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctors')->withTrashed();
    }

    /**
     * Get the Doctors that owns the Appointment.
     */
    public function city()
    {
        return $this->belongsTo('App\Models\Cities')->withTrashed();
    }

    /**
     * Get the Doctors that owns the Appointment.
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Locations')->withTrashed();
    }

    /**
     * Get the Lead that owns the Appointment.
     */
    public function lead()
    {
        return $this->belongsTo('App\Models\Leads')->withTrashed();
    }

    /**
     * Get the patient that owns the Appointment.
     */
    public function patient()
    {
        return $this->belongsTo('App\Models\Patients')->withTrashed();
    }

    /**
     * Get the appointments for City.
     */
    public function sms_logs()
    {
        return $this->hasMany('App\Models\SMSLogs', 'appointment_id')->withTrashed();
    }

    /**
     * Prepare SMS Contnet for Delivery
     *
     * @param: int $appointment_id
     * @param: int $smsContent
     *
     * @return: string
     */
    static public function prepareSMSContent($appointment_id = false, $smsContent)
    {
        if(!$appointment_id) {
            return $smsContent;
        } else {
            // Load Globar Setting for Head Office
            $Setting = Settings::find(5);

            $smsContent = str_replace('##head_office_phone##', $Setting->data, $smsContent);

            $appointment = self::find($appointment_id);
            $patient = Patients::find($appointment->patient_id);

            if($appointment) {
                // Replace Patient Information
                $smsContent = str_replace('##patient_name##', $patient->full_name, $smsContent);
                $smsContent = str_replace('##patient_phone##', $patient->phone, $smsContent);

                // Replace Schedule Information
                $smsContent = str_replace('##appointment_date##', Carbon::parse($appointment->scheduled_date)->format('D, d/m/Y'), $smsContent);
                $smsContent = str_replace('##appointment_time##', Carbon::parse($appointment->scheduled_time)->format('h:i A'), $smsContent);

                // Load and Replace Centre Information
                $Location = Locations::find($appointment->location_id);
                if($Location) {
                    $smsContent = str_replace('##fdo_name##', $Location->fdo_name, $smsContent);
                    $smsContent = str_replace('##fdo_phone##', GeneralFunctions::prepareNumber4Call($Location->fdo_phone), $smsContent);
                    $smsContent = str_replace('##centre_name##', $Location->name, $smsContent);
                    $smsContent = str_replace('##centre_address##', $Location->address, $smsContent);
                    $smsContent = str_replace('##centre_google_map##', $Location->google_map, $smsContent);
                }

                // Load and Replace Doctor Information
                $Doctor = Doctors::find($appointment->doctor_id);
                if($Doctor) {
                    $smsContent = str_replace('##doctor_name##', $Doctor->name, $smsContent);
                    $smsContent = str_replace('##doctor_profile_link##', $Doctor->profile_url, $smsContent);
                }

            }

            return $smsContent;
        }
    }
}
