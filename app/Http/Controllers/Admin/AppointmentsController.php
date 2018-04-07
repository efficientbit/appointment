<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GeneralFunctions;
use App\Models\AppointmentStatuses;
use App\Models\CancellationReasons;
use App\Models\Cities;
use App\Models\Doctors;
use App\Models\Leads;
use App\Models\LeadSources;
use App\Models\Locations;
use App\Models\Appointments;
use App\Models\Patients;
use App\Models\Settings;
use App\Models\SMSLogs;
use App\Models\SMSTemplates;
use App\Models\Treatments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateAppointmentsRequest;
use Auth;
use Config;

// Use Telenor SMS APIs
use App\Helpers\TelenorSMSAPI;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of Appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }

        $cities = Cities::getActiveSortedFeatured();
        $cities->prepend('All','');

        $doctors = Doctors::get()->pluck('name','id');
        $doctors->prepend('All','');

        $locations = Locations::get()->pluck('name','id');
        $locations->prepend('All','');

        $treatments = Treatments::get()->pluck('name','id');
        $treatments->prepend('All','');

        $appointment_statuses = AppointmentStatuses::get()->pluck('name','id');
        $appointment_statuses->prepend('All','');

        return view('admin.appointments.index', compact('cities', 'doctors', 'locations', 'treatments', 'appointment_statuses'));
    }

    /**
     * Display a listing of Lead_statuse.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $where = array();

        $orderBy = 'created_at';
        $order = 'desc';

        if($request->get('order')[0]['dir']) {
            $orderColumn = $request->get('order')[0]['column'];
            $orderBy = $request->get('columns')[$orderColumn]['data'];
            $order = $request->get('order')[0]['dir'];
        }


        if($request->get('full_name') && $request->get('full_name') != '') {
            $where[] = array(
                'patients.full_name',
                'like',
                '%' . $request->get('full_name') . '%'
            );
        }
        if($request->get('phone') && $request->get('phone') != '') {
            $where[] = array(
                'patients.phone',
                'like',
                '%' . GeneralFunctions::cleanNumber($request->get('phone')) . '%'
            );
        }
        if($request->get('date_from') && $request->get('date_from') != '') {
            $where[] = array(
                'appointments.scheduled_date',
                '>=',
                $request->get('date_from')
            );
        }
        if($request->get('date_to') && $request->get('date_to') != '') {
            $where[] = array(
                'appointments.scheduled_date',
                '<=',
                $request->get('date_to')
            );
        }
        if($request->get('doctor_id') && $request->get('doctor_id') != '') {
            $where[] = array(
                'doctor_id',
                '=',
                $request->get('doctor_id')
            );
        }
        if($request->get('city_id') && $request->get('city_id') != '') {
            $where[] = array(
                'city_id',
                '=',
                $request->get('city_id')
            );
        }
        if($request->get('location_id') && $request->get('location_id') != '') {
            $where[] = array(
                'location_id',
                '=',
                $request->get('location_id')
            );
        }
        if($request->get('treatment_id') && $request->get('treatment_id') != '') {
            $where[] = array(
                'treatment_id',
                '=',
                $request->get('treatment_id')
            );
        }

        if(count($where)) {
            $iTotalRecords = Appointments::join('patients','patients.id','=','appointments.patient_id')->where($where)->count();
        } else {
            $iTotalRecords = Appointments::join('patients','patients.id','=','appointments.patient_id')->count();
        }


        $iDisplayLength = intval($request->get('length'));
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($request->get('start'));
        $sEcho = intval($request->get('draw'));

        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if(count($where)) {
            $Appointments = Appointments::join('patients','patients.id','=','appointments.patient_id')->where($where)->select('*', 'appointments.id as app_id')->limit($iDisplayLength)->offset($iDisplayStart)->orderBy($orderBy, $order)->get();
        } else {
            $Appointments = Appointments::join('patients','patients.id','=','appointments.patient_id')->select('*', 'appointments.id as app_id')->limit($iDisplayLength)->offset($iDisplayStart)->orderBy($orderBy, $order)->get();
        }

        if($Appointments) {
            $index = 0;
            foreach($Appointments as $appointment) {
                $records["data"][$index] = array(
                    'full_name' => $appointment->full_name,
                    'phone' => '<a href="javascript:void(0)" class="clipboard" data-toggle="tooltip" title="Click to Copy" data-clipboard-text="' . GeneralFunctions::prepareNumber4Call($appointment->phone) . '">' . GeneralFunctions::prepareNumber4Call($appointment->phone) . '</a>',
                    'scheduled_date' => Carbon::parse($appointment->scheduled_date, null)->format('M j, Y') . ' at ' . Carbon::parse($appointment->scheduled_time, null)->format('h:i A'),
                    'doctor_id' => view('admin.appointments.doctor', compact('appointment'))->render(),
                    'city_id' => $appointment->city_id ? $appointment->city->name : 'N/A',
                    'location_id' => $appointment->location_id ? $appointment->location->name : 'N/A',
                    'treatment_id' => view('admin.appointments.treatment', compact('appointment'))->render(),
                    'appointment_status_id' => '<a id="appointment' . $appointment->app_id . '" href="' . route('admin.appointments.showappointmentstatus',['id' => $appointment->app_id]) . '" data-target="#ajax" data-toggle="modal">' . ($appointment->appointment_status_id ? $appointment->appointment_status->name : '') . '</a>',
                    'actions' => view('admin.appointments.actions', compact('appointment'))->render(),
                );
                $index++;
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Appointments = Appointments::whereIn('id', $request->get('id'));
            if($Appointments) {
                $Appointments->delete();
            }
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Records has been deleted successfully!"; // pass custom message(useful for getting status of group actions)
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return response()->json($records);
    }

    /**
     * Show the form for creating new Appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }

        if($request->get('lead_id')) {
            $lead = Leads::where(['id' => $request->get('lead_id')])->first();
            if($lead) {
                $lead = array(
                    'id' => $lead->id,
                    'patient_id' => $lead->patient_id,
                    'full_name' => ($lead->patient_id) ? $lead->patient->full_name : null,
                    'phone' => ($lead->patient_id) ? $lead->patient->phone : null,
                );
            } else {
                $lead = array(
                    'id' => '',
                    'patient_id' => '',
                    'full_name' => '',
                    'phone' => '',
                );
            }
        } else {
            $lead = array(
                'id' => '',
                'patient_id' => '',
                'full_name' => '',
                'phone' => '',
            );
        }

        $cities = Cities::getActiveFeaturedOnly();

        $lead_sources = LeadSources::getActiveSorted();
        $lead_sources->prepend('Select a Lead Source','');

        $treatments = Treatments::get()->pluck('name','id');
        $treatments->prepend('Select a Treatment','');

        return view('admin.appointments.create', compact('cities','lead', 'lead_sources', 'treatments'));
    }

    /**
     * Store a newly created Appointment in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateAppointmentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateAppointmentsRequest $request)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }

        // Store form data in a variable
        $appointmentData = $request->all();
        $appointmentData['phone'] = GeneralFunctions::cleanNumber($appointmentData['phone']);
        $appointmentData['created_by'] = Auth::user()->id;
        $appointmentData['updated_by'] = Auth::user()->id;
        $appointmentData['scheduled_time'] = Carbon::parse($appointmentData['scheduled_time'])->format('H:i');
        // Set Appointment Status
        $appointmentData['appointment_status_id'] = Config::get('constants.appointment_status_pending');

        /*
         * Check if Lead ID not provided then create a new lead
         * and assign this lead to current appointment.
         */
        if(!$request->get('lead_id')) {
            // Find and update patient, if not found then create patient.
            $patient = Patients::updateOrCreate(array('Phone' => $appointmentData['phone']), $appointmentData);

            $leadObj = $appointmentData;
            unset($leadObj['lead_id']); // Remove Lead ID index
            $leadObj['patient_id'] = $patient->id;
            // Convert Lead status to Converted
            $leadObj['lead_status_id'] = Config::get('constants.lead_status_converted');

            $lead = Leads::create($leadObj);
        } else {
            $lead = Leads::findOrFail($request->get('lead_id'));
            $patient = Patients::findOrFail($lead->patient_id);
            $patient->update($appointmentData);
        }

        // Set Lead ID for Appointment
        $appointmentData['patient_id'] = $patient->id;
        $appointmentData['lead_id'] = $lead->id;
        /*
         * End Lead ID Process
         */

        // Get Location object to retrieve City
        $location = Locations::findOrFail($appointmentData['location_id']);

        // Set City ID after retrieving from Location
        $appointmentData['city_id'] = $location->city_id;

        $appointment = Appointments::create($appointmentData);

        // If Lead ID provided then change it's status to converted
        if($request->get('lead_id') && $request->get('lead_id')) {
            $lead = Leads::findOrFail($request->get('lead_id'));
            if($lead) {
                $lead->update(['lead_status_id' => Config::get('constants.lead_status_converted')]);
            }
        }

        // Send SMS via API
        $response = $this->sendSMS($appointment->id, $patient->phone);
        if($response['status']) {
            // Message is sent so set flag to true
            $appointment->update(array('msg_count' => 1));
            flash('Record has been created successfully. SMS Status: Sent')->success()->important();
        } else {
            flash('Record has been created successfully. SMS Error: ' . $response['error_msg'])->success()->important();
        }

        return redirect()->route('admin.appointments.index');
    }

    /*
     * Send SMS on booking of Appointment
     *
     * @param: int $appointmentId
     * @param: string $patient_phone
     * @return: array|mixture
     */
    private function sendSMS($appointmentId, $patient_phone) {
        // SEND SMS for Appointment Booked
        $SMSTemplate = SMSTemplates::findOrFail(1); // 1 for Appointment SMS
        $preparedText = Appointments::prepareSMSContent($appointmentId, $SMSTemplate->content);

        $Settings = Settings::get()->getDictionary();
        $SMSObj = array(
            'username' => $Settings[1]->data, // Setting ID 1 for Username
            'password' => $Settings[2]->data, // Setting ID 2 for Password
            'to' => GeneralFunctions::prepareNumber(GeneralFunctions::cleanNumber($patient_phone)),
            'text' => $preparedText,
            'mask' => $Settings[3]->data, // Setting ID 3 for Mask
            'test_mode' => $Settings[4]->data, // Setting ID 3 Test Mode
        );

        $response = TelenorSMSAPI::SendSMS($SMSObj);

        $SMSLog = array_merge($SMSObj, $response);
        $SMSLog['appointment_id'] = $appointmentId;
        $SMSLog['created_by'] = Auth::user()->id;
        SMSLogs::create($SMSLog);
        // SEND SMS for Appointment Booked End

        return $response;
    }

    /**
     * Re-Send SMS for Appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_sms($id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        $appointment = Appointments::findOrFail($id);
        $patient = Patients::findOrFail($appointment->patient_id);
        // Send SMS via API
        $response = $this->sendSMS($appointment->id, $patient->phone);
        if($response['status']) {
            // Message is sent so set flag to true
            $data['msg_count'] = $appointment->msg_count + 1;
            flash('SMS has been sent successfully. SMS Status: Sent')->success()->important();
        } else {
            flash('Unable to sent SMS. SMS Error: ' . $response['error_msg'])->error()->important();
        }
        $appointment->update($data);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Show details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }

        $appointment = Appointments::findOrFail($id);

        return view('admin.appointments.detailTo', compact('appointment'));
    }


    /**
     * Show the form for editing Appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        $appointment = Appointments::findOrFail($id);

        $treatments = Treatments::get()->pluck('name','id');
        $treatments->prepend('Select a Treatment','');

        $cities = Cities::getActiveFeaturedOnly();

        return view('admin.appointments.edit', compact('appointment', 'cities', 'treatments'));
    }

    /**
     * Update Appointment in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateAppointmentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateAppointmentsRequest $request, $id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        $appointment = Appointments::findOrFail($id);

        $appointmentData = $request->all();
        $appointmentData['updated_by'] = Auth::user()->id;
        $appointmentData['scheduled_time'] = Carbon::parse($appointmentData['scheduled_time'])->format('H:i');

        /*
         * Perform Lead Operations
         */
        $lead = Leads::findOrFail($appointmentData['lead_id']);
        $patient = Patients::findOrFail($lead->patient_id);
        $patient->update($appointmentData);
        /*
         * Lead Operations End
         */

        // Send SMS via API
        $response = $this->sendSMS($appointment->id, $patient->phone);
        if($response['status']) {
            // Message is sent so set flag to true
            $appointmentData['msg_count'] = $appointment->msg_count + 1;
            flash('Record has been updated successfully. SMS Status: Sent')->success()->important();
        } else {
            flash('Record has been updated successfully. SMS Error: ' . $response['error_msg'])->success()->important();
        }
        $appointment->update($appointmentData);

        return redirect()->route('admin.appointments.index');
    }


    /**
     * Remove Appointment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        $appointment = Appointments::findOrFail($id);
        $appointment->delete();

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        $permission = Cities::findOrFail($id);
        $permission->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        $permission = Cities::findOrFail($id);
        $permission->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Delete all selected Appointment at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('appointments_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Appointments::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }



    /**
     * Delete all selected Appointment at once.
     *
     * @param Request $request
     * @return  Response $response
     */
    public function loadLeadData(Request $request)
    {
        $status = 0;
        $patient_id = 0;

        if (Gate::allows('appointments_manage') && $request->get('phone') && !$request->get('lead_id')) {

            $phone = GeneralFunctions::cleanNumber($request->get('phone'));
            $patient = Patients::where(['phone' => $phone])->select('id')->first();

            if(!$patient) {
                $status = 1;
            } else {
                $patient_id = $patient->id;
            }
        }

        return response()->json(array('status' => $status, 'patient_id' => $patient_id));
    }

    /**
     * Load all Doctors
     *
     * @param Request $request
     * @return  Response $response
     */
    public function loadDoctors(Request $request)
    {
        if($request->get('app_id')) {
            $appointment = Appointments::find($request->get('app_id'));
            if(!$appointment) {
                return response()->json(array());
            }

            $doctors = Doctors::where(['location_id' => $appointment->location_id])->select('id', 'name')->get();
        } else {
            $doctors = Doctors::get()->pluck('name', 'id');
        }

        $data = array();

        if($doctors) {
            foreach($doctors as $doctor) {
                $data[] = array(
                    'value' => $doctor->id,
                    'text' => $doctor->name,
                );
            }
        }

        return response()->json($data);
    }

    /**
     * Store Data.
     *
     * @param Request $request
     * @return  Response $response
     */
    public function saveDoctor(Request $request)
    {
        if (! Gate::allows('appointments_manage')) {
            return response()->json(array('status' => 0));
        } else {
            $id = $request->get('pk');;
            $doctor_id = $request->get('value');;

            // Check if Lead found or not
            $appointment = Appointments::find($id);
            if(!$appointment) {
                return response()->json(array('status' => 0));
            } else {
                $appointment->update(['doctor_id' => $doctor_id]);
                return response()->json(array('status' => 1));
            }
        }
    }

    /**
     * Load all Treatments
     *
     * @param Request $request
     * @return  Response $response
     */
    public function loadTreatments(Request $request)
    {
        $treatments = Treatments::select('name', 'id')->get();

        $data = array();

        if($treatments) {
            foreach($treatments as $treatment) {
                $data[] = array(
                    'value' => $treatment->id,
                    'text' => $treatment->name,
                );
            }
        }

        return response()->json($data);
    }

    /**
     * Store Data.
     *
     * @param Request $request
     * @return  Response $response
     */
    public function saveTreatment(Request $request)
    {
        if (! Gate::allows('appointments_manage')) {
            return response()->json(array('status' => 0));
        } else {
            $id = $request->get('pk');;
            $treatment_id = $request->get('value');;

            // Check if Lead found or not
            $appointment = Appointments::find($id);
            if(!$appointment) {
                return response()->json(array('status' => 0));
            } else {
                $appointment->update(['treatment_id' => $treatment_id]);
                return response()->json(array('status' => 1));
            }
        }
    }

    /**
     * Load all Appointment Statuses.
     *
     * @param Request $request
     */
    public function showAppointmentStatuses(Request $request)
    {

        $appointment_statuses = AppointmentStatuses::getActiveSorted();
        $appointment_statuses->prepend('Change Status','');

        $cancellation_reasons = CancellationReasons::getActiveSorted();
        $cancellation_reasons->prepend('Change Reason','');

        $appointment = Appointments::findOrFail($request->get('id'));

        return view('admin.appointments.appointment_status', compact('appointment', 'appointment_statuses', 'cancellation_reasons'));
    }

    /**
     * Update Appointment Status
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateAppointmentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAppointmentStatuses(Request $request)
    {
        $data = $request->all();

        if(Config::get('constants.appointment_status_not_show') != $data['appointment_status_id']) {
            // Not Show is not set, Make empty reason field.
            $data['cancellation_reason_id'] = null;
            $data['reason'] = null;
        } else {
            if(Config::get('constants.cancellation_reason_other_reason') != $data['cancellation_reason_id']) {
                $data['reason'] = null;
            }
        }

        $appointment = Appointments::findOrFail($request->get('id'));
        $appointment->update($data);

        return response()->json(['status' => 1]);
    }

}
