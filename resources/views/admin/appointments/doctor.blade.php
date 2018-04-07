<a href="{{ route('admin.appointments.doctors',['app_id' => $appointment->app_id]) }}"
   class="doctor" data-type="select"
   data-pk="{{ $appointment->app_id }}" data-value="@if($appointment->doctor_id){{ $appointment->doctor_id }}@else{{''}}@endif"
   data-source="{{ route('admin.appointments.doctors',['app_id' => $appointment->app_id]) }}" data-title="Change Doctor">
    @if($appointment->city_id){{ $appointment->doctor->name }}@else{{''}}@endif
</a>