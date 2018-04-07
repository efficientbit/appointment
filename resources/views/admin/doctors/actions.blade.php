@if($doctor->active)
    {!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.doctors.inactive', $doctor->id])) !!}
    {!! Form::submit(trans('global.app_inactive'), array('class' => 'btn btn-xs btn-warning')) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'PATCH',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.doctors.active', $doctor->id])) !!}
    {!! Form::submit(trans('global.app_active'), array('class' => 'btn btn-xs btn-primary')) !!}
    {!! Form::close() !!}
@endif
<a href="{{ route('admin.doctors.edit',[$doctor->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
{!! Form::open(array(
    'style' => 'display: inline-block;',
    'method' => 'DELETE',
    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
    'route' => ['admin.doctors.destroy', $doctor->id])) !!}
{!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
{!! Form::close() !!}