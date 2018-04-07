<?php

namespace App\Http\Controllers\Admin;

use App\Models\CancellationReasons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateCancellationReasonsRequest;
use DB;
use Illuminate\Support\Facades\Input;

class CancellationReasonsController extends Controller
{
    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }

        $cancellation_reasons = CancellationReasons::all();

        return view('admin.cancellation_reasons.index', compact('cancellation_reasons'));
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
            $iTotalRecords = CancellationReasons::where($where)->count();
        } else {
            $iTotalRecords = CancellationReasons::count();
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
            $CancellationReasons = CancellationReasons::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $CancellationReasons = CancellationReasons::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($CancellationReasons) {
            foreach($CancellationReasons as $cancellation_reason) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$cancellation_reason->id.'"/><span></span></label>',
                    'name' => $cancellation_reason->name,
                    'actions' => view('admin.cancellation_reasons.actions', compact('cancellation_reason'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $CancellationReasons = CancellationReasons::whereIn('id', $request->get('id'));
            if($CancellationReasons) {
                $CancellationReasons->delete();
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
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        return view('admin.cancellation_reasons.create');
    }

    public function sortorder(){

        $cancellation_reasons=DB::table('cancellation_reasons')->orderby('sort_no','ASC')->get();
        return view('admin.cancellation_reasons.Sort',compact('cancellation_reasons'));
    }
    public function sortorder_save(){

        $cancellation_reasons=DB::table('cancellation_reasons')->orderBy('sort_no','ASC')->get();
        $itemID=Input::get('itemID');
        $itemIndex=Input::get('itemIndex');
        if($itemID){
            foreach ($cancellation_reasons as $lead) {
                $sort=DB::table('cancellation_reasons')->where('id', '=', $itemID)->update(array('sort_no' => $itemIndex));
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
     * @param  \App\Http\Requests\Admin\StoreUpdateCancellationReasonsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateCancellationReasonsRequest $request)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        CancellationReasons::create($request->all());

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.cancellation_reasons.index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        $cancellation_reason = CancellationReasons::findOrFail($id);

        return view('admin.cancellation_reasons.edit', compact('cancellation_reason'));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateCancellationReasonsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateCancellationReasonsRequest $request, $id)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        $cancellation_reason = CancellationReasons::findOrFail($id);
        $cancellation_reason->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.cancellation_reasons.index');
    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        $cancellation_reason = CancellationReasons::findOrFail($id);
        $cancellation_reason->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.cancellation_reasons.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        $cancellation_reason = CancellationReasons::findOrFail($id);
        $cancellation_reason->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.cancellation_reasons.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        $cancellation_reason = CancellationReasons::findOrFail($id);
        $cancellation_reason->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.cancellation_reasons.index');
    }

    /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('cancellation_reasons_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = CancellationReasons::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
