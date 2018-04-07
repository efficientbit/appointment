<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateCitiesRequest;
use DB;
use Illuminate\Support\Facades\Input;

class CitiesController extends Controller
{
    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }

        $cities = Cities::all();

        return view('admin.cities.index', compact('cities'));
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

        if($request->get('name')) {
            $where[] = array(
                'name',
                'like',
                '%' . $request->get('name') . '%'
            );
        }

        if($request->get('is_featured') != '') {
            $where[] = array(
                'is_featured',
                '=',
                $request->get('is_featured')
            );
        }

        if(count($where)) {
            $iTotalRecords = Cities::where($where)->count();
        } else {
            $iTotalRecords = Cities::count();
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
            $Cities = Cities::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $Cities = Cities::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($Cities) {
            foreach($Cities as $citie) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$citie->id.'"/><span></span></label>',
                    'name' => $citie->name,
                    'is_featured' => $citie->is_featured ? 'Yes' : 'No',
                    'actions' => view('admin.cities.actions', compact('citie'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Cities = Cities::whereIn('id', $request->get('id'));
            if($Cities) {
                $Cities->delete();
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
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }

        $city = DB::table('cities')->orderby('sort_number','ASC')->get();
        return view('admin.cities.create',compact('city'));
    }


    public function sortorder_save(){

        $city=DB::table('cities')->orderBy('sort_number','ASC')->get();
        $itemID=Input::get('itemID');
        $itemIndex=Input::get('itemIndex');
        if($itemID){
            foreach ($city as $cit) {
                $sort=DB::table('cities')->where('id', '=', $itemID)->update(array('sort_number' => $itemIndex));
                $myarray=['status'=>"Data Sort Successfully"];
                return response()->json($myarray);
            }
        }
        else{
            $myarray=['status'=>"Data Not Sort"];
            return response()->json($myarray);
        }
    }

    public function sortorder(){

        $city = DB::table('cities')->orderby('sort_number','ASC')->get();
        return view('admin.cities.SortCity',compact('city'));
    }
    /**
     * Store a newly created Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateCitiesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateCitiesRequest $request)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        Cities::create($request->all());

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.cities.index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        $citie = Cities::findOrFail($id);

        return view('admin.cities.edit', compact('citie'));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateCitiesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateCitiesRequest $request, $id)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        $citie = Cities::findOrFail($id);
        $citie->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.cities.index');
    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        $citie = Cities::findOrFail($id);
        $citie->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.cities.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        $citie = Cities::findOrFail($id);
        $citie->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.cities.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        $citie = Cities::findOrFail($id);
        $citie->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.cities.index');
    }

    /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('cities_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Cities::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }



}
