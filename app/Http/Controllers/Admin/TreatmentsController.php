<?php

namespace App\Http\Controllers\Admin;

use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateTreatmentsRequest;
use DB;
use Illuminate\Support\Facades\Input;

class TreatmentsController extends Controller
{
    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }

        $treatments = Treatments::all();

        return view('admin.treatments.index', compact('treatments'));
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
            $iTotalRecords = Treatments::where($where)->count();
        } else {
            $iTotalRecords = Treatments::count();
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
            $Treatments = Treatments::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $Treatments = Treatments::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($Treatments) {
            foreach($Treatments as $treatment) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$treatment->id.'"/><span></span></label>',
                    'name' => $treatment->name,
                    'actions' => view('admin.treatments.actions', compact('treatment'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Treatments = Treatments::whereIn('id', $request->get('id'));
            if($Treatments) {
                $Treatments->delete();
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
     * Show the form for creating new Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        return view('admin.treatments.create');
    }

    public function sortorder(){

        $treatments=DB::table('treatments')->orderby('sort_no','ASC')->get();
        return view('admin.treatments.Sort',compact('treatments'));
    }
    public function sortorder_save(){

        $treatments=DB::table('treatments')->orderBy('sort_no','ASC')->get();
        $itemID=Input::get('itemID');
        $itemIndex=Input::get('itemIndex');
        if($itemID){
            foreach ($treatments as $lead) {
                $sort=DB::table('treatments')->where('id', '=', $itemID)->update(array('sort_no' => $itemIndex));
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
     * Store a newly created Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateTreatmentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateTreatmentsRequest $request)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        Treatments::create($request->all());

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.treatments.index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        $treatment = Treatments::findOrFail($id);

        return view('admin.treatments.edit', compact('treatment'));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateTreatmentsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateTreatmentsRequest $request, $id)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        $treatment = Treatments::findOrFail($id);
        $treatment->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.treatments.index');
    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        $treatment = Treatments::findOrFail($id);
        $treatment->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.treatments.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        $treatment = Treatments::findOrFail($id);
        $treatment->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.treatments.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        $treatment = Treatments::findOrFail($id);
        $treatment->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.treatments.index');
    }

    /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('treatments_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Treatments::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
