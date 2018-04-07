<a href="{{ route('admin.leads.cities') }}"
   class="city" data-type="select"
   data-pk="{{ $lead->id }}" data-value="@if($lead->city_id){{ $lead->city->id }}@else{{''}}@endif"
   data-source="{{ route('admin.leads.cities') }}" data-title="Select a City">
    @if($lead->city_id){{ $lead->city->name }}@else{{''}}@endif
</a>