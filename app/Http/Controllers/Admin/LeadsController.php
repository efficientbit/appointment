<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TelenorSMSAPI;
use App\Http\Requests\Admin\StoreUpdateLeadCommentsRequest;
use App\Models\Cities;
use App\Models\LeadComments;
use App\Models\Leads;
use App\Models\LeadSources;
use App\Models\LeadStatuses;
use App\Models\Patients;
use App\Models\Settings;
use App\Models\SMSLogs;
use App\Models\SMSTemplates;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateLeadsRequest;
use App\Http\Requests\Admin\FileUploadLeadsRequest;
use Auth;
use File;
//Excel Library
use App\Helpers\GeneralFunctions;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Config;

class LeadsController extends Controller
{
    /**
     * Display a listing of Lead.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }

        $cities = Cities::getActiveSorted();
        $cities->prepend('All','');

        $users = User::get()->pluck('name','id');
        $users->prepend('All','');

        $lead_statuses = LeadStatuses::getActiveSorted(Config::get('constants.lead_status_junk'));
        $lead_statuses->prepend('All','');
        return view('admin.leads.index', compact('cities', 'users', 'lead_statuses'));
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
        if($request->get('city_id') && $request->get('city_id') != '') {
            $where[] = array(
                'city_id',
                '=',
                $request->get('city_id')
            );
        }
        if($request->get('lead_status_id') && $request->get('lead_status_id')) {
            $where[] = array(
                'lead_status_id',
                '=',
                $request->get('lead_status_id')
            );
        }
        if($request->get('created_by') && $request->get('created_by') != '') {
            $where[] = array(
                'leads.created_by',
                '=',
                $request->get('created_by')
            );
        }

        if(count($where)) {
            $iTotalRecords = Leads::join('patients','patients.id','=','leads.patient_id')->where($where)->whereNotIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->count();
        } else {
            $iTotalRecords = Leads::join('patients','patients.id','=','leads.patient_id')->whereNotIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->count();
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
            $Leads = Leads::join('patients','patients.id','=','leads.patient_id')->whereNotIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->where($where)->select('*', 'leads.created_by as lead_created_by')->limit($iDisplayLength)->offset($iDisplayStart)->orderBy($orderBy, $order)->get();
        } else {
            $Leads = Leads::join('patients','patients.id','=','leads.patient_id')->whereNotIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->select('*', 'leads.created_by as lead_created_by')->limit($iDisplayLength)->offset($iDisplayStart)->orderBy($orderBy, $order)->get();
        }

        $Users = User::get()->getDictionary();

        if($Leads) {
            $index = 0;
            foreach($Leads as $lead) {
                $records["data"][$index] = array(
                    'full_name' => $lead->full_name,
                    'phone' => '<a href="javascript:void(0)" class="clipboard" data-toggle="tooltip" title="Click to Copy" data-clipboard-text="' . GeneralFunctions::prepareNumber4Call($lead->patient->phone) . '">' . GeneralFunctions::prepareNumber4Call($lead->patient->phone) . '</a>',
                    'city_id' => view('admin.leads.city', compact('lead'))->render(),
                    'lead_status_id' => view('admin.leads.lead_status', compact('lead'))->render(),
                    'created_by' => array_key_exists($lead->lead_created_by, $Users) ? $Users[$lead->lead_created_by]->name : 'N/A',
                    'actions' => view('admin.leads.actions', compact('lead'))->render(),
                );
                $index++;
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Leads = Leads::whereIn('id', $request->get('id'));
            if($Leads) {
                $Leads->delete();
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
     * Show the form for creating new Lead.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }

        $cities = Cities::getActiveSorted();
        $cities->prepend('Select a City','');

        $lead_sources = LeadSources::getActiveSorted();
        $lead_sources->prepend('Select a Lead Source','');

        $lead_statuses = LeadStatuses::getActiveSorted();
        $lead_statuses->prepend('Select a Lead Status','');

        // Create an empty Patient Object
        $lead = new \stdClass();
        $lead->patient = new \stdClass();
        $lead->patient->id = null;
        $lead->patient->full_name = null;
        $lead->patient->email = null;
        $lead->patient->phone = null;
        $lead->patient->gender = null;

        return view('admin.leads.create', compact('cities', 'lead_sources', 'lead_statuses', 'lead'));
    }

    /**
     * Store a newly created Lead in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateLeadsRequest $request)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $data = $request->all();
        $data['phone'] = GeneralFunctions::cleanNumber($data['phone']);

        // Set Create and Update by
        $data['created_by'] = Auth::user()->id;
        $data['updated_by'] = Auth::user()->id;

        // Find and update patient, if not found then create patient.
        $patient = Patients::updateOrCreate(array('Phone' => $data['phone']), $data);

        // Update Patient ID
        $data['patient_id'] = $patient->id;

        $lead = Leads::create($data);

        // Send SMS via API
//        $response = $this->sendSMS($lead->id, $lead->phone);
//        if($response['status']) {
//            // Message is sent so set flag to true
//            $lead->update(array('msg_count' => 1));
//            flash('Record has been created successfully. SMS Status: Sent')->success()->important();
//        } else {
//            flash('Record has been created successfully. SMS Error: ' . $response['error_msg'])->success()->important();
//        }
        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.leads.index');
    }

    /*
     * Send SMS on booking of Appointment
     *
     * @param: int $leadId
     * @param: string $patient_phone
     * @return: array|mixture
     */
    private function sendSMS($leadId, $phone) {
        // SEND SMS for Appointment Booked
        $SMSTemplate = SMSTemplates::findOrFail(2); // 2 for Leads SMS
        $preparedText = Leads::prepareSMSContent($leadId, $SMSTemplate->content);

        $Settings = Settings::get()->getDictionary();
        $SMSObj = array(
            'username' => $Settings[1]->data, // Setting ID 1 for Username
            'password' => $Settings[2]->data, // Setting ID 2 for Password
            'to' => GeneralFunctions::prepareNumber(GeneralFunctions::cleanNumber($phone)),
            'text' => $preparedText,
            'mask' => $Settings[3]->data, // Setting ID 3 for Mask
            'test_mode' => $Settings[4]->data, // Setting ID 3 Test Mode
        );

        $response = TelenorSMSAPI::SendSMS($SMSObj);

        $SMSLog = array_merge($SMSObj, $response);
        $SMSLog['lead_id'] = $leadId;
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
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);
        $patient = Patients::findOrFail($lead->patient_id);

        if(!$lead->msg_count) {
            // Send SMS via API
            $response = $this->sendSMS($lead->id, $patient->phone);
            if($response['status']) {
                // Message is sent so set flag to true
                $data['msg_count'] = $lead->msg_count + 1;
                flash('SMS has been sent successfully. SMS Status: Sent')->success()->important();
            } else {
                flash('Unable to sent SMS. SMS Error: ' . $response['error_msg'])->error()->important();
            }
            $lead->update($data);
        } else {
            flash('SMS is already delivered to this lead, Can\'t deliver another SMS.')->warning()->important();
        }

        return redirect()->route('admin.leads.index');
    }

    /**
     * Show Lead detail.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);

        return view('admin.leads.detailTo', compact('lead'));
    }

    /**
     * Store a newly created Lead in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadCommentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function comment_store(StoreUpdateLeadCommentsRequest $request)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }

        $data = $request->all();
        // Set Created by
        $data['created_by'] = Auth::user()->id;
        $lead = LeadComments::create($data);

        flash('Comment has been added successfully.')->success()->important();

        return redirect()->back();
    }


    /**
     * Show the form for editing Lead.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);

        $cities = Cities::getActiveSorted();
        $cities->prepend('Select a City','');

        $lead_sources = LeadSources::getActiveSorted();
        $lead_sources->prepend('Select a Lead Source','');

        $lead_statuses = LeadStatuses::getActiveSorted();
        $lead_statuses->prepend('Select a Lead Status','');

        return view('admin.leads.editTo', compact('lead', 'cities', 'lead_sources', 'lead_statuses'));
    }

    /**
     * Update Lead in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateLeadsRequest $request, $id)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);

        // Get all request data into a var
        $data = $request->all();
        $data['phone'] = GeneralFunctions::cleanNumber($data['phone']);

        // Find and update patient, if not found then create patient.
        $patient = Patients::updateOrCreate(array('id' => $data['patient_id']), $data);

        if(!$lead->msg_count) {
            // Send SMS via API
            $response = $this->sendSMS($lead->id, $patient->phone);
            if($response['status']) {
                // Message is sent so set flag to true
                $data['msg_count'] = $lead->msg_count + 1;
                flash('Record has been updated successfully. SMS Status: Sent')->success()->important();
            } else {
                flash('Record has been updated successfully. SMS Error: ' . $response['error_msg'])->success()->important();
            }
        } else {
            flash('Record has been updated successfully.')->success()->important();
        }
        $lead->update($data);
        return redirect()->back();
        //return redirect()->route('admin.leads.index');
    }


    /**
     * Remove Lead from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);
        $lead->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.leads.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);
        $lead->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.leads.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        $lead = Leads::findOrFail($id);
        $lead->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.leads.index');
    }

    /**
     * Delete all selected Lead at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Leads::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

    /**
     * Load all Lead Statuses.
     *
     * @param Request $request
     */
    public function showLeadStatuses(Request $request)
    {

        $lead_statuses = LeadStatuses::getActiveSorted();
        $lead_statuses->prepend('Select a Lead Status','');

        $lead = Leads::findOrFail($request->get('id'));

        return view('admin.leads.lead_status_popup', compact('lead', 'lead_statuses'));
    }

    /**
     * Update Lead Status
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeLeadStatuses(Request $request)
    {
        $data = $request->all();

        $lead = Leads::findOrFail($request->get('id'));
        $lead->update($data);
        // Set Created by
        $data['created_by'] = Auth::user()->id;
        $data['lead_id'] = $lead->id;
        $lead = LeadComments::create($data);

        return response()->json(['status' => 1]);
    }

    /**
     * Load all Lead Statuses.
     *
     * @param Request $request
     */
    public function loadLeadStatuses(Request $request)
    {
        $lead_statuses = LeadStatuses::getActiveOnly();

        $data = array();

        if($lead_statuses) {
            foreach($lead_statuses as $lead_status) {
                $data[] = array(
                    'value' => $lead_status->id,
                    'text' => $lead_status->name,
                );
            }
        }

        return response()->json($data);
    }

    /**
     * Store Lead Status.
     *
     * @param Request $request
     */
    public function saveLeadStatus(Request $request)
    {
        if (! Gate::allows('leads_manage')) {
            return response()->json(array('status' => 0));
        } else {
            $id = $request->get('pk');;
            $lead_status_id = $request->get('value');;

            // Check if Lead found or not
            $lead = Leads::find($id);
            if(!$lead) {
                return response()->json(array('status' => 0));
            } else {
                $data = array(
                    'lead_status_id' => $lead_status_id
                );
                if($lead_status_id != Config::get('constants.lead_status_junk')) {
                    if(!$lead->msg_count) {
                        $patient = Patients::find($id);
                        // Lead Status is not junk, Send SMS now
                        $response = $this->sendSMS($lead->id, $patient->phone);
                        if($response['status']) {
                            // Message is sent so set flag to true
                            $data['msg_count'] = $lead->msg_count + 1;
                        }
                    }
                }

                $lead->update($data);

                return response()->json(array('status' => 1));
            }
        }

    }

    /**
     * Load all Lead Sources.
     *
     * @param Request $request
     */
    public function loadLeadSources(Request $request)
    {
        $lead_sources = LeadSources::getActiveOnly();

        $data = array();

        if($lead_sources) {
            foreach($lead_sources as $lead_source) {
                $data[] = array(
                    'value' => $lead_source->id,
                    'text' => $lead_source->name,
                );
            }
        }

        return response()->json($data);
    }

    /**
     * Store Lead Status.
     *
     * @param Request $request
     */
    public function saveLeadSource(Request $request)
    {
        if (! Gate::allows('leads_manage')) {
            return response()->json(array('status' => 0));
        } else {
            $id = $request->get('pk');;
            $lead_source_id = $request->get('value');;

            // Check if Lead found or not
            $lead = Leads::find($id);
            if(!$lead) {
                return response()->json(array('status' => 0));
            } else {
                $lead->update(['lead_source_id' => $lead_source_id]);
                return response()->json(array('status' => 1));
            }
        }
    }

    /**
     * Load all Lead Citys.
     *
     * @param Request $request
     */
    public function loadCities(Request $request)
    {
        $cities = Cities::getActiveOnly();

        $data = array();

        if($cities) {
            foreach($cities as $citie) {
                $data[] = array(
                    'value' => $citie->id,
                    'text' => $citie->name,
                );
            }
        }

        return response()->json($data);
    }

    /**
     * Store Lead Status.
     *
     * @param Request $request
     */
    public function saveCity(Request $request)
    {
        if (! Gate::allows('leads_manage')) {
            return response()->json(array('status' => 0));
        } else {
            $id = $request->get('pk');;
            $lead_source_id = $request->get('value');;

            // Check if Lead found or not
            $lead = Leads::find($id);
            if(!$lead) {
                return response()->json(array('status' => 0));
            } else {
                $lead->update(['city_id' => $lead_source_id]);
                return response()->json(array('status' => 1));
            }
        }
    }

    /**
     * Store Lead Status.
     *
     * @param Request $request
     */
    public function importLeads(Request $request)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }

        return view('admin.leads.import');
    }

    /**
     * Update Lead in storage.
     *
     * @param  \App\Http\Requests\Admin\FileUploadLeadsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadLeads(FileUploadLeadsRequest $request)
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }

        if($request->hasfile('leads_file'))
        {
            // Check if directory not exists then create it
            $dir = public_path('/leadsdata');
            if(!File::isDirectory($dir)) {
                // path does not exist so create directory
                File::makeDirectory($dir, 777, true, true);
                File::put($dir . '/index.html', 'Direct access is forbidden');
            }

            $File = $request->file('leads_file');

            // Store File Information
            $name = str_replace('.'. $File->getClientOriginalExtension(), '', $File->getClientOriginalName());
            $ext = $File->getClientOriginalExtension();
            $full_name = $File->getClientOriginalName();
            $full_name_new = $name . '-' . rand(11111111,99999999) . '.' . $ext;

            $File->move($dir, $full_name_new);


            // Read File and dump data
            $SpreadSheet = IOFactory::load($dir. DIRECTORY_SEPARATOR . $full_name_new);
            $SheetData = $SpreadSheet->getActiveSheet(0)->toArray(null, true, true, true);

            if(count($SheetData)) {

                if(isset($SheetData[1])) {
                    // Prepare Source Status, Source Source and City data for comparision
                    $Cities = Cities::get()->pluck('id','name');
                    $leadSources = LeadSources::get()->pluck('id','name');
                    $LeadStatuses = LeadStatuses::get()->pluck('id','name');

                    // Array to hold phone numbers which will be used to find duplicates if any
                    $dupPhone_list = array();
                    $dupPhones = array();
                    $allPatientMapping = array();

                    // Iterate over the data
                    foreach($SheetData as $SingleRow) {
                        // Provided Sheet columns should match
                        if (
                            trim(strtolower($SingleRow['A'])) == 'full name' &&
                            trim(strtolower($SingleRow['B'])) == 'email' &&
                            trim(strtolower($SingleRow['C'])) == 'phone'
                        ) {
                            // Row contains headers so ignore this line
                            continue;
                        }

                        // Process Phone Number
                        $dupPhone_list[] = GeneralFunctions::cleanNumber(trim($SingleRow['C']));
                    }

                    /*
                     * Step A: Start
                     * Find patients who are not in system and create them
                     */
                    if(count($dupPhone_list)) {

                        // Find duplicate records in System.
                        $dupPhones = Patients::whereIn('phone', $dupPhone_list)->select('phone','id')->get()->keyBy('phone');
                        if($dupPhones) {
                            $dupPhones = $dupPhones->toArray();
                        } else {
                            // Restore Old state again
                            $dupPhones = array();
                        }

                        $newPatientPhones = array(); /* New Patient Phones Array */
                        $found_patients = Patients::whereIn('phone', $dupPhone_list)->select('phone')->get()->pluck('phone');
                        if($found_patients) {
                            $newPatientPhones = array_diff($dupPhone_list, $found_patients->toArray());
                        }

                        // New Patients found so this is time to create new patients into system
                        if(count($newPatientPhones)) {
                            $newPatientsData = array(); /* New Patients Array */
                            // Iterate over the data
                            foreach($SheetData as $SingleRow) {
                                // Provided Sheet columns should match
                                if (
                                    trim(strtolower($SingleRow['A'])) == 'full name' &&
                                    trim(strtolower($SingleRow['B'])) == 'email' &&
                                    trim(strtolower($SingleRow['C'])) == 'phone'
                                ) {
                                    // Row contains headers so ignore this line
                                    continue;
                                }

                                // Process Phone Number
                                $phone = GeneralFunctions::cleanNumber(trim($SingleRow['C']));

                                // If Phone found in new customers array then prepare data
                                if(in_array($phone, $newPatientPhones)) {

                                    // Process Gender
                                    $gender = 0;
                                    if(trim(strtolower($SingleRow['D'])) == 'male') {
                                        $gender = 1; // 1 for Male, Check constants.php
                                    } else if(trim(strtolower($SingleRow['D'])) == 'female') {
                                        $gender = 2; // 2 for Female, Check constants.php
                                    }

                                    $newPatientsData[] = array(
                                        'full_name' => $SingleRow['A'],
                                        'email' => $SingleRow['B'],
                                        'phone' => $phone,
                                        'gender' => $gender,
                                    );
                                }
                            }
                            // Create Patient Profiles now
                            Patients::insert($newPatientsData);
                        }

                        $allPatientMapping = Patients::whereIn('phone', $dupPhone_list)->select('phone','id')->get()->keyBy('phone');
                        if(count($allPatientMapping)) {
                            $allPatientMapping = $allPatientMapping->toArray();
                        } else {
                            $allPatientMapping = array();
                        }
                    }
                    /*
                     * Step A: End
                     */

                    // Var to hold all Leads Data
                    $LeadData = array();

                    // Iterate over the data
                    foreach($SheetData as $SingleRow) {
                        // Provided Sheet columns should match
                        if(
                            trim(strtolower($SingleRow['A'])) == 'full name' &&
                            trim(strtolower($SingleRow['B'])) == 'email' &&
                            trim(strtolower($SingleRow['C'])) == 'phone'
                        ) {
                            // Row contains headers so ignore this line
                            continue;
                        }

                        // Process Phone Number
                        $phone = GeneralFunctions::cleanNumber(trim($SingleRow['C']));

                        // Process Gender
                        $gender = 0;
                        if(trim(strtolower($SingleRow['D'])) == 'male') {
                            $gender = 1; // 1 for Male, Check constants.php
                        } else if(trim(strtolower($SingleRow['D'])) == 'female') {
                            $gender = 2; // 2 for Female, Check constants.php
                        }

                        // Process City
                        $city_id = null;
                        $city = trim(strtolower($SingleRow['E']));
                        if($Cities && $city) {
                            foreach($Cities as $CityName => $CityId) {
                                if($city == trim(strtolower($CityName))) {
                                    $city_id = $CityId;
                                }
                            }
                        }

                        // Process Lead Source
                        $lead_source_id = Config::get('constants.lead_source_social_media');
                        $lead_source = trim(strtolower($SingleRow['F']));
                        if($leadSources && $lead_source) {
                            foreach($leadSources as $SrcName => $SrcId) {
                                if($lead_source == trim(strtolower($SrcName))) {
                                    $lead_source_id = $SrcId;
                                } else {
                                    $lead_source_id = Config::get('constants.lead_source_social_media');
                                }
                            }
                        }

                        // Process Lead Status
                        $lead_status_id = Config::get('constants.lead_status_open');
                        $lead_status = trim(strtolower($SingleRow['G']));
                        if($LeadStatuses && $lead_status) {
                            foreach($LeadStatuses as $StatusName => $StatusId) {
                                if($lead_status == trim(strtolower($StatusName))) {
                                    $lead_status_id = $StatusId;
                                } else {
                                    $lead_status_id = Config::get('constants.lead_status_open');
                                }
                            }
                        }

                        // If Phone found in existing records skip this
                        if(array_key_exists($phone, $dupPhones)) {
                            Leads::updateOrCreate(array(
                                'patient_id' => $allPatientMapping[$phone]['id']
                            ), array(
                                'city_id' => $city_id,
                                'lead_source_id' => $lead_source_id,
                                'lead_status_id' => $lead_status_id,
                                'created_by' => Auth::User()->id,
                                'updated_by' => Auth::User()->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ));
                            continue;
                        }

                        $LeadData[] = array(
                            'patient_id' => $allPatientMapping[$phone]['id'],
                            'city_id' => $city_id,
                            'lead_source_id' => $lead_source_id,
                            'lead_status_id' => $lead_status_id,
                            'created_by' => Auth::User()->id,
                            'updated_by' => Auth::User()->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );
                    }

                    // If Get some recors insert them now
                    if(count($LeadData)) {
                        Leads::insert($LeadData);
                    }

                    // Invalid data is provided
                    flash('Leads has been imported. Created: ' . count($LeadData) . ', Duplicates: ' . count($dupPhones))->success()->important();

                    return redirect()->route('admin.leads.index');
                } else {
                    flash('Invalid data provided.')->error()->important();
                }
            } else {
                flash('No input file specified..')->error()->important();
            }

            return redirect()->route('admin.leads.import');
        }
    }

    /**
     * Display a listing of Junk Lead.
     *
     * @return \Illuminate\Http\Response
     */
    public function junk()
    {
        if (! Gate::allows('leads_manage')) {
            return abort(401);
        }

        $cities = Cities::getActiveSorted();
        $cities->prepend('All','');

        $users = User::get()->pluck('name','id');
        $users->prepend('All','');

        $lead_statuses = LeadStatuses::getActiveSorted(false, Config::get('constants.lead_status_junk'));
        $lead_statuses->prepend('All','');

        return view('admin.leads.junk', compact('cities', 'users', 'lead_statuses'));
    }

    /**
     * Display a listing of Lead_statuse.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function junkDatatable(Request $request)
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
        if($request->get('city_id') && $request->get('city_id') != '') {
            $where[] = array(
                'city_id',
                '=',
                $request->get('city_id')
            );
        }
        if($request->get('lead_status_id') && $request->get('lead_status_id')) {
            $where[] = array(
                'lead_status_id',
                '=',
                $request->get('lead_status_id')
            );
        }
        if($request->get('created_by') && $request->get('created_by') != '') {
            $where[] = array(
                'leads.created_by',
                '=',
                $request->get('created_by')
            );
        }

        if(count($where)) {
            $iTotalRecords = Leads::join('patients','patients.id','=','leads.patient_id')->where($where)->whereIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->count();
        } else {
            $iTotalRecords = Leads::join('patients','patients.id','=','leads.patient_id')->whereIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->count();
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
            $Leads = Leads::join('patients','patients.id','=','leads.patient_id')->whereIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->where($where)->select('*', 'leads.created_by as lead_created_by')->limit($iDisplayLength)->offset($iDisplayStart)->orderBy($orderBy, $order)->get();
        } else {
            $Leads = Leads::join('patients','patients.id','=','leads.patient_id')->whereIn('leads.lead_status_id', array(Config::get('constants.lead_status_junk')))->select('*', 'leads.created_by as lead_created_by')->limit($iDisplayLength)->offset($iDisplayStart)->orderBy($orderBy, $order)->get();
        }

        $Users = User::get()->getDictionary();

        if($Leads) {
            $index = 0;
            foreach($Leads as $lead) {
                $records["data"][$index] = array(
                    'full_name' => $lead->full_name,
                    'phone' => '<a href="javascript:void(0)" class="clipboard" data-toggle="tooltip" title="Click to Copy" data-clipboard-text="' . GeneralFunctions::prepareNumber4Call($lead->patient->phone) . '">' . GeneralFunctions::prepareNumber4Call($lead->patient->phone) . '</a>',
                    'city_id' => view('admin.leads.city', compact('lead'))->render(),
                    'lead_status_id' => view('admin.leads.lead_status', compact('lead'))->render(),
                    'created_by' => array_key_exists($lead->lead_created_by, $Users) ? $Users[$lead->lead_created_by]->name : 'N/A',
                    'actions' => view('admin.leads.actions', compact('lead'))->render(),
                );
                $index++;
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Leads = Leads::whereIn('id', $request->get('id'));
            if($Leads) {
                $Leads->delete();
            }
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Records has been deleted successfully!"; // pass custom message(useful for getting status of group actions)
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        return response()->json($records);
    }

    /*Start Comment function for lead*/
    public function LeadStoreComment(Request $req){
        $leadComment=LeadComments::where('lead_id','=',$req->lead_id)->get();
        $lead=new LeadComments();
        $lead->comment=$req->comment;
        $lead->lead_id=$req->lead_id;
        $lead->created_by= Auth::user()->id;
        $leadCommentDate=\Carbon\Carbon::parse($lead->created_at)->format('D M, j Y h:i A');
        $lead->save();
        $username=Auth::user()->name;
        $myarray=['username'=>$username,'lead'=>$lead,'leadCommentDate'=>$leadCommentDate,'leadCommentSection'=>$leadComment];
        return response()->json($myarray);
    }
    /*End Comment function for lead*/
}
