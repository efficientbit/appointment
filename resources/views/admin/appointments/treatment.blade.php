<a href="{{ route('admin.appointments.treatments',['app_id' => $appointment->id]) }}"
   class="treatment" data-type="select"
   data-pk="{{ $appointment->id }}" data-value="@if($appointment->treatment_id){{ $appointment->treatment_id }}@else{{''}}@endif"
   data-source="{{ route('admin.appointments.treatments',['app_id' => $appointment->id]) }}" data-title="Change Treatment">
    @if($appointment->city_id){{ $appointment->treatment->name }}@else{{''}}@endif
</a>