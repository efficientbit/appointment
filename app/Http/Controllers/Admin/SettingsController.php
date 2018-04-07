<?php

namespace App\Http\Controllers\Admin;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateSettingsRequest;
use DB;
use Illuminate\Support\Facades\Input;

class SettingsController extends Controller
{
    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }

        $settings = Settings::all();

        return view('admin.settings.index', compact('settings'));
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

        if($request->get('setting_name')) {
            $where[] = array(
                'name',
                'like',
                '%' . $request->get('setting_name') . '%'
            );
        }

        if($request->get('setting_data')) {
            $where[] = array(
                'data',
                'like',
                '%' . $request->get('setting_data') . '%'
            );
        }

        if(count($where)) {
            $iTotalRecords = Settings::where($where)->count();
        } else {
            $iTotalRecords = Settings::count();
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
            $Settings = Settings::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $Settings = Settings::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($Settings) {
            foreach($Settings as $setting) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$setting->id.'"/><span></span></label>',
                    'name' => $setting->name,
                    'data' => ($setting->id == 4) ? (($setting->data == '1') ? 'Yes' : 'No') :$setting->data,
                    'actions' => view('admin.settings.actions', compact('setting'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $Settings = Settings::whereIn('id', $request->get('id'));
            if($Settings) {
                $Settings->delete();
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
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }

        $setting = new \stdClass();
        $setting->id = null;

        return view('admin.settings.create', compact('setting'));
    }


    /**
     * Store a newly created Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateSettingsRequest $request)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        Settings::create($request->all());

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.settings.index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        $setting = Settings::findOrFail($id);

        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateSettingsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateSettingsRequest $request, $id)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        $setting = Settings::findOrFail($id);
        $setting->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.settings.index');
    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        $setting = Settings::findOrFail($id);
        $setting->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.settings.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        $setting = Settings::findOrFail($id);
        $setting->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.settings.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        $setting = Settings::findOrFail($id);
        $setting->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.settings.index');
    }

    /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('settings_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Settings::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
