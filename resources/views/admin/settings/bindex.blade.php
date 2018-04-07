@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="page-title col-md-10">@lang('global.settings.title')</h1>
        <p class="col-md-2">
            <a href="{{ route('admin.settings.create') }}" class="btn btn-success pull-right">@lang('global.app_add_new')</a>
        </p>
    </section>

    <div class="clearfix"></div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('global.app_list')</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped {{ count($settings) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        <th>@lang('global.settings.fields.name')</th>
                        <th>@lang('global.settings.fields.data')</th>
                        <th width="18%">@lang('global.settings.fields.actions')</th>

                    </tr>
                    </thead>

                    <tbody>
                    @if (count($settings) > 0)
                        @foreach ($settings as $setting)
                            <tr data-entry-id="{{ $setting->id }}">
                                <td></td>
                                <td>{{ $setting->name }}</td>
                                <td>
                                    @if($setting->id == 4)
                                        @if($setting->data == '1'){{ 'Yes' }}@else{{ 'No' }}@endif
                                    @else
                                        {{ $setting->data }}
                                    @endif
                                </td>
                                <td>
                                    @if($setting->active)
                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'PATCH',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.settings.inactive', $setting->id])) !!}
                                        {!! Form::submit(trans('global.app_inactive'), array('class' => 'btn btn-xs btn-warning')) !!}
                                        {!! Form::close() !!}
                                    @else
                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'PATCH',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.settings.active', $setting->id])) !!}
                                        {!! Form::submit(trans('global.app_active'), array('class' => 'btn btn-xs btn-primary')) !!}
                                        {!! Form::close() !!}
                                    @endif
                                    <a href="{{ route('admin.settings.edit',[$setting->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.settings.destroy', $setting->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
@stop

@section('javascript') 
    <script>
        window.route_mass_crud_entries_destroy = '{{ route('admin.settings.mass_destroy') }}';
    </script>
@endsection