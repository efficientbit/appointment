<a class="btn btn-xs btn-warning" href="{{ route('admin.leads.detail',[$lead->id]) }}" data-target="#ajax_leads_detail" data-toggle="modal"><i class="fa fa-eye"></i></a>
<a class="btn btn-xs btn-info" href="{{ route('admin.leads.edit',[$lead->id]) }}" data-target="#ajax_leads_edit" data-toggle="modal"><i class="fa fa-edit"></i></a>
@if(Gate::allows('appointments_manage') && (Config::get('constants.lead_status_converted') != $lead->lead_status_id))
    <a href="{{ route('admin.appointments.create',['lead_id' => $lead->id]) }}" class="btn btn-xs btn-success" data-toggle="tooltip" title="Convert"><i class="fa fa-recycle"></i></a>
@endif