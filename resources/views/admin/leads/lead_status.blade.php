{{--<a href="{{ route('admin.leads.lead_statuses') }}"--}}
   {{--class="lead_status" data-type="select"--}}
   {{--data-pk="{{ $lead->id }}" data-value="@if($lead->lead_status_id){{ $lead->lead_status->id }}@else{{''}}@endif"--}}
   {{--data-source="{{ route('admin.leads.lead_statuses') }}" data-title="Select a Status">--}}
    {{--@if($lead->lead_status_id){{ $lead->lead_status->name }}@else{{''}}@endif--}}
{{--</a>--}}
<a id="lead{{ $lead->id }}" href="{{ route('admin.leads.showleadstatus',['id' => $lead->id]) }}" data-target="#ajax" data-toggle="modal">@if($lead->lead_status_id){{ $lead->lead_status->name }}@else{{''}}@endif</a>