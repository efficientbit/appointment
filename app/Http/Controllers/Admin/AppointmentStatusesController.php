<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppointmentStatuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateAppointmentStatusesRequest;
use DB;
use Illuminate\Support\Facades\Input;

class AppointmentStatusesController extends Controller
{
    /**
     * Display a listing of Appointment_statuse.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }

        $appointment_statuses = AppointmentStatuses::all();

        return view('admin.appointment_statuses.index', compact('appointment_statuses'));
    }

    /**
     * Display a listing of Appointment_statuse.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $where = array();

        if($request->get('appointment_status_name')) {
            $where[] = array(
                'name',
                'like',
                '%' . $request->get('appointment_status_name') . '%'
            );
        }

        if(count($where)) {
            $iTotalRecords = AppointmentStatuses::where($where)->count();
        } else {
            $iTotalRecords = AppointmentStatuses::count();
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
            $AppointmentStatuses = AppointmentStatuses::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $AppointmentStatuses = AppointmentStatuses::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($AppointmentStatuses) {
            foreach($AppointmentStatuses as $appointment_statuse) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$appointment_statuse->id.'"/><span></span></label>',
                    'name' => $appointment_statuse->name,
                    'actions' => view('admin.appointment_statuses.actions', compact('appointment_statuse'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $AppointmentStatuses = AppointmentStatuses::whereIn('id', $request->get('id'));
            if($AppointmentStatuses) {
                $AppointmentStatuses->delete();
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
     * Show the form for creating new Appointment_statuse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }

        return view('admin.appointment_statuses.create');
    }

    public function sortorder(){

        $appointment_status=DB::table('appointment_statuses')->orderby('sort_no','ASC')->get();
        return view('admin.appointment_statuses.Sort',compact('appointment_status'));
    }

    public function sortorder_save(){

        $appointment_status=DB::table('appointment_statuses')->orderBy('sort_no','ASC')->get();
        $itemID=Input::get('itemID');
        $itemIndex=Input::get('itemIndex');
        if($itemID){
            foreach ($appointment_status as $appointment) {
                $sort=DB::table('appointment_statuses')->where('id', '=', $itemID)->update(array('sort_no' => $itemIndex));
                $myarray=['status'=>"Data Sort Successfully"];
                return response()->json($myarray);
            }
        }
        else{
            $myarray=['status'=>"Data Not Sort"];
            return response()->json($myarray);
        }
    }

    /**
     * Store a newly created Appointment_statuse in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateAppointmentStatusesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateAppointmentStatusesRequest $request)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }

        $appointment_statuse = AppointmentStatuses::create($request->all());
        $appointment_statuse->update(['sort_no' => $appointment_statuse->id]);

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.appointment_statuses.index');
    }


    /**
     * Show the form for editing Appointment_statuse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }
        $appointment_statuse = AppointmentStatuses::findOrFail($id);

        return view('admin.appointment_statuses.edit', compact('appointment_statuse'));
    }

    /**
     * Update Appointment_statuse in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateAppointmentStatusesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateAppointmentStatusesRequest $request, $id)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }
        $appointment_statuse = AppointmentStatuses::findOrFail($id);
        $appointment_statuse->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.appointment_statuses.index');
    }


    /**
     * Remove Appointment_statuse from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }
        $appointment_statuse = AppointmentStatuses::findOrFail($id);
        $appointment_statuse->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.appointment_statuses.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }
        $appointment_statuse = AppointmentStatuses::findOrFail($id);
        $appointment_statuse->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.appointment_statuses.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }
        $appointment_statuse = AppointmentStatuses::findOrFail($id);
        $appointment_statuse->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.appointment_statuses.index');
    }

    /**
     * Delete all selected Appointment_statuse at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('appointment_statuses_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = AppointmentStatuses::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
