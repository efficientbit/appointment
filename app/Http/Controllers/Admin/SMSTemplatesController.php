<?php

namespace App\Http\Controllers\Admin;

use App\Models\SMSTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateSMSTemplatesRequest;
use DB;

class SMSTemplatesController extends Controller
{
    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }

        $sms_templates = SMSTemplates::all();

        return view('admin.sms_templates.index', compact('sms_templates'));
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

        if($request->get('lead_status_content')) {
            $where[] = array(
                'content',
                'like',
                '%' . $request->get('lead_status_content') . '%'
            );
        }

        if(count($where)) {
            $iTotalRecords = SMSTemplates::where($where)->count();
        } else {
            $iTotalRecords = SMSTemplates::count();
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
            $SMSTemplates = SMSTemplates::where($where)->limit($iDisplayLength)->offset($iDisplayStart)->get();
        } else {
            $SMSTemplates = SMSTemplates::limit($iDisplayLength)->offset($iDisplayStart)->get();
        }

        if($SMSTemplates) {
            foreach($SMSTemplates as $sms_template) {
                $records["data"][] = array(
                    'id' => '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$sms_template->id.'"/><span></span></label>',
                    'name' => $sms_template->name,
                    'content' => substr($sms_template->content, 0, 70) . '...',
                    'actions' => view('admin.sms_templates.actions', compact('sms_template'))->render(),
                );
            }
        }

        if ($request->get('customActionType') && $request->get('customActionType') == "group_action") {
            $SMSTemplates = SMSTemplates::whereIn('id', $request->get('id'));
            if($SMSTemplates) {
                $SMSTemplates->delete();
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
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        return view('admin.sms_templates.create');
    }


    /**
     * Store a newly created Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateSMSTemplatesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateSMSTemplatesRequest $request)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        SMSTemplates::create($request->all());

        flash('Record has been created successfully.')->success()->important();

        return redirect()->route('admin.sms_templates.index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        $sms_template = SMSTemplates::findOrFail($id);

        return view('admin.sms_templates.edit', compact('sms_template'));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUpdateSMSTemplatesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateSMSTemplatesRequest $request, $id)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        $sms_template = SMSTemplates::findOrFail($id);
        $sms_template->update($request->all());

        flash('Record has been updated successfully.')->success()->important();

        return redirect()->route('admin.sms_templates.index');
    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        $sms_template = SMSTemplates::findOrFail($id);
        $sms_template->delete();

        flash('Record has been deleted successfully.')->success()->important();

        return redirect()->route('admin.sms_templates.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inactive($id)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        $sms_template = SMSTemplates::findOrFail($id);
        $sms_template->update(['active' => 0]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.sms_templates.index');
    }

    /**
     * Inactive Record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function active($id)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        $sms_template = SMSTemplates::findOrFail($id);
        $sms_template->update(['active' => 1]);

        flash('Record has been inactivated successfully.')->success()->important();

        return redirect()->route('admin.sms_templates.index');
    }

    /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('sms_templates_manage')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = SMSTemplates::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
