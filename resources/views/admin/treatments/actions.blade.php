@if($treatment->active)
    {!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.treatments.inactive', $treatment->id])) !!}
    {!! Form::submit(trans('global.app_inactive'), array('class' => 'btn btn-xs btn-warning')) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.treatments.active', $treatment->id])) !!}
    {!! Form::submit(trans('global.app_active'), array('class' => 'btn btn-xs btn-primary')) !!}
    {!! Form::close() !!}
@endif
<a href="{{ route('admin.treatments.edit',[$treatment->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
{!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'DELETE',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.treatments.destroy', $treatment->id])) !!}
{!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
{!! Form::close() !!}