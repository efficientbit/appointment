<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeadStatuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateLeadStatusesRequest;
use DB;
use Illuminate\Support\Facades\Input;

class LeadStatusesController extends Controller
{
    /**
     * Display a listing of Lead_statuse.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }

        $lead_statuses = LeadStatuses::all();

        return view('admin.lead_statuses.index', compact('lead_statuses'));
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

        if($request->get('lead_status_name')) {
            $where[] = array(
                'name',
                'like',
                '%' . $request->get('lead_status_name') . '%'
            );
        }

        if(count($where)) {
            $iTotalRecords = LeadStatuses::where($where)->count();
        } else {
            $iTotalRecords = LeadStatuses::count();
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
            $LeadStatuses = LeadStatuses::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $LeadStatuses = LeadStatuses::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($LeadStatuses) {
            foreach($LeadStatuses as $lead_statuse) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$lead_statuse->id.'"/><span></span></label>',
                    'name' => $lead_statuse->name,
                    'actions' => view('admin.lead_statuses.actions', compact('lead_statuse'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $LeadStatuses = LeadStatuses::whereIn('id', $request->get('id'));
            if($LeadStatuses) {
                $LeadStatuses->delete();
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
     * Show the form for creating new Lead_statuse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }

        return view('admin.lead_statuses.create');
    }

    public function sortorder(){

        $lead_status=DB::table('lead_statuses')->orderby('sort_no','ASC')->get();
        return view('admin.lead_statuses.Sort',compact('lead_status'));
    }

    public function sortorder_save(){

        $lead_status=DB::table('lead_statuses')->orderBy('sort_no','ASC')->get();
        $itemID=Input::get('itemID');
        $itemIndex=Input::get('itemIndex');
        if($itemID){
            foreach ($lead_status as $lead) {
                $sort=DB::table('lead_statuses')->where('id', '=', $itemID)->update(array('sort_no' => $itemIndex));
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
     * Store a newly created Lead_statuse in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadStatusesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateLeadStatusesRequest $request)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }

        $lead_statuse = LeadStatuses::create($request->all());
        $lead_statuse->update(['sort_no' => $lead_statuse->id]);

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.lead_statuses.index');
    }


    /**
     * Show the form for editing Lead_statuse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }
        $lead_statuse = LeadStatuses::findOrFail($id);

        return view('admin.lead_statuses.edit', compact('lead_statuse'));
    }

    /**
     * Update Lead_statuse in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadStatusesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateLeadStatusesRequest $request, $id)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }
        $lead_statuse = LeadStatuses::findOrFail($id);
        $lead_statuse->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.lead_statuses.index');
    }


    /**
     * Remove Lead_statuse from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }
        $lead_statuse = LeadStatuses::findOrFail($id);
        $lead_statuse->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.lead_statuses.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }
        $lead_statuse = LeadStatuses::findOrFail($id);
        $lead_statuse->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.lead_statuses.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }
        $lead_statuse = LeadStatuses::findOrFail($id);
        $lead_statuse->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.lead_statuses.index');
    }

    /**
     * Delete all selected Lead_statuse at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('lead_statuses_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = LeadStatuses::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
