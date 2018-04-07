@extends('layouts.app')

@section('title')
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title">@lang('global.roles.title')</h1>
    <!-- END PAGE TITLE-->
@endsection


@section('content')
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption font-green-sharp">
                <i class="icon-plus font-green-sharp"></i>
                <span class="caption-subject bold uppercase"> @lang('global.app_create')</span>
            </div>
            <div class="actions">
                <a href="{{ route('admin.roles.index') }}" class="btn dark pull-right">@lang('global.app_back')</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-group">
                {!! Form::open(['method' => 'POST', 'id' => 'form-validation', 'route' => ['admin.roles.store']]) !!}
                    <div class="form-body">
                        <!-- Starts Form Validation Messages -->
                    @include('partials.messages')
                    <!-- Ends Form Validation Messages -->

                        <div class="form-group">
                            {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                            {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('name'))
                                <p class="help-block">
                                    {{ $errors->first('name') }}
                                </p>
                            @endif
                        </div>

                        <div class="form-group">
                            {!! Form::label('permission', 'Permissions', ['class' => 'control-label']) !!}
                            {!! Form::select('permission[]', $permissions, old('permission'), ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('permission'))
                                <p class="help-block">
                                    {{ $errors->first('permission') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="form-actions">
                        {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-success']) !!}
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="{{ url('metronic/assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('metronic/assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('js/admin/roles/fields.js') }}" type="text/javascript"></script>
@endsection

