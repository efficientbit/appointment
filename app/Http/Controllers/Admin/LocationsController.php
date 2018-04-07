<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GeneralFunctions;
use App\Models\Cities;
use App\Models\Locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateLocationsRequest;

class LocationsController extends Controller
{
    /**
     * Display a listing of Location.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }

        $cities = Cities::getActiveSorted();
        $cities->prepend('Select a City','');

        $locations = Locations::all();

        return view('admin.locations.index', compact('locations','cities'));
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

        if($request->get('lead_status_fdo_name')) {
            $where[] = array(
                'fdo_name',
                'like',
                '%' . $request->get('lead_status_fdo_name') . '%'
            );
        }

        if($request->get('lead_status_fdo_phone')) {
            $where[] = array(
                'fdo_phone',
                'like',
                '%' . $request->get('lead_status_fdo_phone') . '%'
            );
        }

        if($request->get('lead_status_address')) {
            $where[] = array(
                'address',
                'like',
                '%' . $request->get('lead_status_address') . '%'
            );
        }

        if($request->get('lead_status_city')) {
            $where[] = array(
                'city_id',
                '=',
                $request->get('lead_status_city')
            );
        }

        if(count($where)) {
            $iTotalRecords = Locations::where($where)->count();
        } else {
            $iTotalRecords = Locations::count();
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
            $Locations = Locations::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $Locations = Locations::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($Locations) {
            foreach($Locations as $location) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$location->id.'"/><span></span></label>',
                    'name' => $location->name,
                    'fdo_name' => $location->fdo_name ? $location->fdo_name : 'N/A',
                    'fdo_phone' => $location->fdo_phone ? GeneralFunctions::prepareNumber4Call($location->fdo_phone) : 'N/A',
                    'address' => $location->address,
                    'city' => $location->city->name,
                    'actions' => view('admin.locations.actions', compact('location'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Locations = Locations::whereIn('id', $request->get('id'));
            if($Locations) {
                $Locations->delete();
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
     * Show the form for creating new Location.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }

        $cities = Cities::get()->pluck('name','id');
        $cities->prepend('Select a City','');

        return view('admin.locations.create', compact('cities'));
    }

    /**
     * Store a newly created Location in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLocationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateLocationsRequest $request)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        $data = $request->all();
        $data['fdo_phone'] = GeneralFunctions::cleanNumber($data['fdo_phone']);
        Locations::create($data);

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.locations.index');
    }


    /**
     * Show the form for editing Location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        $location = Locations::findOrFail($id);
        $cities = Cities::get()->pluck('name','id');
        $cities->prepend('Select a City','');

        return view('admin.locations.edit', compact('location', 'cities'));
    }

    /**
     * Update Location in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateLocationsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateLocationsRequest $request, $id)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        $location = Locations::findOrFail($id);

        $data = $request->all();
        $data['fdo_phone'] = GeneralFunctions::cleanNumber($data['fdo_phone']);

        $location->update($data);

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.locations.index');
    }


    /**
     * Remove Location from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        $location = Locations::findOrFail($id);
        $location->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.locations.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        $location = Locations::findOrFail($id);
        $location->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.locations.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        $location = Locations::findOrFail($id);
        $location->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.locations.index');
    }

    /**
     * Delete all selected Location at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('locations_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Locations::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
