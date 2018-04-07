<div class="modal-body">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">@lang('global.app_detail')</h4>
    </div>
    <table class="table table-striped">
        <tbody>
        <tr>
            <th>Patient Name</th>
            <td>{{ $appointment->patient->full_name }}</td>
            <th>Patient Phone</th>
            <td>@if($appointment->patient->phone){{ \App\Helpers\GeneralFunctions::prepareNumber4Call($appointment->patient->phone) }}@else{{'N/A'}}@endif</td>
        </tr>
        <tr>
            <th>Appointment Time</th>
            <td>{{ \Carbon\Carbon::parse($appointment->scheduled_date, null)->format('M j, y') . ' at ' . \Carbon\Carbon::parse($appointment->scheduled_time, null)->format('h:i A') }}</td>
            <th>Doctor</th>
            <td>@if($appointment->doctor_id){{ $appointment->doctor->name }}@else{{'N/A'}}@endif</td>
        </tr>
        <tr>
            <th>City</th>
            <td>@if($appointment->city_id){{ $appointment->city->name }}@else{{'N/A'}}@endif</td>
            <th>Centre</th>
            <td>@if($appointment->location_id){{ $appointment->location->name }}@else{{'N/A'}}@endif</td>
        </tr>
        <tr>
            <th>Appointment Status</th>
            <td @if($appointment->appointment_status_id != Config::get('constants.appointment_status_not_show')) colspan="3" @endif>@if($appointment->appointment_status_id){{ $appointment->appointment_status->name }}@else{{'N/A'}}@endif</td>
            @if($appointment->appointment_status_id == Config::get('constants.appointment_status_not_show'))
                <th>{{ trans('global.cancellation_reasons.word') }}</th>
                <td>@if($appointment->cancellation_reason_id && isset($appointment->cancellation_reason->name)){{ $appointment->cancellation_reason->name }}@else{{ 'N/A' }}@endif</td>
            @endif
        </tr>
        @if(
            ($appointment->appointment_status_id == Config::get('constants.appointment_status_not_show')) &&
            ($appointment->cancellation_reason_id == Config::get('constants.cancellation_reason_other_reason'))
        )
            <tr>
                <th>Reason</th>
                <td colspan="3">@if($appointment->reason){{ $appointment->reason }}@else{{ 'N/A' }}@endif</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
