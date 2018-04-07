<a class="btn btn-xs btn-warning" href="{{ route('admin.appointments.detail',[$appointment->app_id]) }}" data-target="#ajax_detail_appointment" data-toggle="modal"><i class="fa fa-eye"></i></a>
<a href="{{ route('admin.appointments.edit',[$appointment->app_id]) }}" class="btn btn-xs btn-info" data-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a>
{!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.appointments.send_sms', $appointment->app_id])) !!}
    <button type="submit" class="btn btn-xs btn-primary" data-toggle="tooltip" title="Send SMS, Total Sent: {{ $appointment->msg_count }}"><i class="fa fa-send-o"></i></button>
{!! Form::close() !!}