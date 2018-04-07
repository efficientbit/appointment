<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cities;
use App\Models\Locations;
use App\Models\Doctors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateDoctorsRequest;

class DoctorsController extends Controller
{
    /**
     * Display a listing of Doctor.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }

        $doctors = Doctors::all();

        $cities = Cities::getActiveSorted();
        $cities->prepend('Select a City','');

        $locations = Locations::getActiveSorted();
        $locations->prepend('Select a Location','');

        return view('admin.doctors.index', compact('doctors', 'cities', 'locations'));
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

        if($request->get('lead_status_city')) {
            $where[] = array(
                'city_id',
                '=',
                $request->get('lead_status_city')
            );
        }

        if($request->get('lead_status_location')) {
            $where[] = array(
                'location_id',
                '=',
                $request->get('lead_status_location')
            );
        }

        if(count($where)) {
            $iTotalRecords = Doctors::where($where)->count();
        } else {
            $iTotalRecords = Doctors::count();
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
            $Doctors = Doctors::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $Doctors = Doctors::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($Doctors) {
            foreach($Doctors as $doctor) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$doctor->id.'"/><span></span></label>',
                    'name' => $doctor->name,
                    'city' => $doctor->city->name,
                    'location' => $doctor->location->name,
                    'actions' => view('admin.doctors.actions', compact('doctor'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Doctors = Doctors::whereIn('id', $request->get('id'));
            if($Doctors) {
                $Doctors->delete();
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
     * Show the form for creating new Doctor.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }

        $locations = Locations::get()->pluck('full_address','id');
        $locations->prepend('Select a Location','');

        return view('admin.doctors.create', compact('locations'));
    }

    /**
     * Store a newly created Doctor in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateDoctorsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateDoctorsRequest $request)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }

        // Store form data in a variable
        $data = $request->all();

        // Get Location object to retrieve City
        $location = Locations::findOrFail($data['location_id']);

        // Set City ID after retrieving from Location
        $data['city_id'] = $location->city_id;

        Doctors::create($data);

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.doctors.index');
    }


    /**
     * Show the form for editing Doctor.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }
        $doctor = Doctors::findOrFail($id);

        $locations = Locations::get()->pluck('full_address','id');
        $locations->prepend('Select a Location','');

        return view('admin.doctors.edit', compact('doctor', 'locations'));
    }

    /**
     * Update Doctor in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateDoctorsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateDoctorsRequest $request, $id)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }
        $doctor = Doctors::findOrFail($id);

        // Store form data in a variable
        $data = $request->all();

        // Get Location object to retrieve City
        $location = Locations::findOrFail($data['location_id']);

        // Set City ID after retrieving from Location
        $data['city_id'] = $location->city_id;

        $doctor->update($data);

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.doctors.index');
    }


    /**
     * Remove Doctor from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }
        $doctor = Doctors::findOrFail($id);
        $doctor->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.doctors.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }
        $doctor = Doctors::findOrFail($id);
        $doctor->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.doctors.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }
        $doctor = Doctors::findOrFail($id);
        $doctor->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.doctors.index');
    }

    /**
     * Delete all selected Doctor at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('doctors_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Doctors::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
