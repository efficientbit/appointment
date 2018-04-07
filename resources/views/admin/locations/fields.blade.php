<div class="col-xs-6 form-group">
    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('name'))
        <p class="help-block">
            {{ $errors->first('name') }}
        </p>
    @endif
</div>
<div class="col-xs-6 form-group">
    {!! Form::label('fdo_name', 'FDO Name*', ['class' => 'control-label']) !!}
    {!! Form::text('fdo_name', old('fdo_name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('fdo_name'))
        <p class="help-block">
            {{ $errors->first('fdo_name') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>
<div class="col-xs-6 form-group">
    {!! Form::label('fdo_phone', 'FDO Phone* (03XXXXXXXXX)', ['class' => 'control-label']) !!}
    {!! Form::number('fdo_phone', old('fdo_phone'), ['min' => 0, 'maxlength' => 11, 'size' => 4,'class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('fdo_phone'))
        <p class="help-block">
            {{ $errors->first('fdo_phone') }}
        </p>
    @endif
</div>
<div class="col-xs-6 form-group">
    {!! Form::label('city_id', 'City*', ['class' => 'control-label']) !!}
    {!! Form::select('city_id',$cities, old('city_id'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('city_id'))
        <p class="help-block">
            {{ $errors->first('city_id') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>
<div class="col-xs-12 form-group">
    {!! Form::label('address', 'Address*', ['class' => 'control-label']) !!}
    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('address'))
        <p class="help-block">
            {{ $errors->first('address') }}
        </p>
    @endif
</div>
<div class="col-xs-12 form-group">
    {!! Form::label('google_map', 'Google Map*', ['class' => 'control-label']) !!}
    {!! Form::text('google_map', old('google_map'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('google_map'))
        <p class="help-block">
            {{ $errors->first('google_map') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>