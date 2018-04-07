@if($location->active)
    {!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.locations.inactive', $location->id])) !!}
    {!! Form::submit(trans('global.app_inactive'), array('class' => 'btn btn-xs btn-warning')) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.locations.active', $location->id])) !!}
    {!! Form::submit(trans('global.app_active'), array('class' => 'btn btn-xs btn-primary')) !!}
    {!! Form::close() !!}
@endif
<a href="{{ route('admin.locations.edit',[$location->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
{!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'DELETE',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.locations.destroy', $location->id])) !!}
{!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
{!! Form::close() !!}