<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeadSources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateLeadSourcesRequest;
use DB;
use Illuminate\Support\Facades\Input;

class LeadSourcesController extends Controller
{
    /**
     * Display a listing of Lead_source.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }

        $lead_sources = LeadSources::all();

        return view('admin.lead_sources.index', compact('lead_sources'));
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
            $iTotalRecords = LeadSources::where($where)->count();
        } else {
            $iTotalRecords = LeadSources::count();
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
            $LeadSources = LeadSources::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $LeadSources = LeadSources::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($LeadSources) {
            foreach($LeadSources as $lead_source) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$lead_source->id.'"/><span></span></label>',
                    'name' => $lead_source->name,
                    'actions' => view('admin.lead_sources.actions', compact('lead_source'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $LeadSources = LeadSources::whereIn('id', $request->get('id'));
            if($LeadSources) {
                $LeadSources->delete();
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
     * Show the form for creating new Lead_source.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }

        return view('admin.lead_sources.create');
    }

    public function sortorder(){

        $lead_source=DB::table('lead_sources')->orderby('sort_no','ASC')->get();
        return view('admin.lead_sources.Sort',compact('lead_source'));
    }
    public function sortorder_save(){

        $lead_source=DB::table('lead_sources')->orderBy('sort_no','ASC')->get();
        $itemID=Input::get('itemID');
        $itemIndex=Input::get('itemIndex');
        if($itemID){
            foreach ($lead_source as $lead) {
                $sort=DB::table('lead_sources')->where('id', '=', $itemID)->update(array('sort_no' => $itemIndex));
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
     * Store a newly created Lead_source in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadSourcesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateLeadSourcesRequest $request)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }

        $lead_source = LeadSources::create($request->all());
        $lead_source->update(['sort_no' => $lead_source->id]);

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.lead_sources.index');
    }


    /**
     * Show the form for editing Lead_source.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }
        $lead_source = LeadSources::findOrFail($id);

        return view('admin.lead_sources.edit', compact('lead_source'));
    }

    /**
     * Update Lead_source in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLeadSourcesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateLeadSourcesRequest $request, $id)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }
        $lead_source = LeadSources::findOrFail($id);
        $lead_source->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.lead_sources.index');
    }


    /**
     * Remove Lead_source from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }
        $lead_source = LeadSources::findOrFail($id);
        $lead_source->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.lead_sources.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }
        $lead_source = LeadSources::findOrFail($id);
        $lead_source->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.lead_sources.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }
        $lead_source = LeadSources::findOrFail($id);
        $lead_source->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.lead_sources.index');
    }

    /**
     * Delete all selected Lead_source at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('lead_sources_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = LeadSources::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
