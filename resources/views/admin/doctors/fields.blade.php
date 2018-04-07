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
    {!! Form::label('location_id', 'Centre*', ['class' => 'control-label']) !!}
    {!! Form::select('location_id',$locations, old('location_id'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('location_id'))
        <p class="help-block">
            {{ $errors->first('location_id') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>