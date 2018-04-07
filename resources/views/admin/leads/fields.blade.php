{!! Form::hidden('patient_id', $lead->patient->id, ['id' => 'patient_id']) !!}
<div class="col-xs-4 form-group">
    {!! Form::label('full_name', 'Full Name*', ['class' => 'control-label']) !!}
    {!! Form::text('full_name', (old('full_name')) ? old('full_name') : $lead->patient->full_name, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('full_name'))
        <p class="help-block">
            {{ $errors->first('full_name') }}
        </p>
    @endif
</div>
<div class="col-xs-4 form-group">
    {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
    {!! Form::email('email', (old('email')) ? old('email') : $lead->patient->email, ['class' => 'form-control', 'placeholder' => '']) !!}
    @if($errors->has('email'))
        <p class="help-block">
            {{ $errors->first('email') }}
        </p>
    @endif
</div>
<div class="col-xs-4 form-group">
    {!! Form::label('phone', 'Phone* (03XXXXXXXXX)', ['class' => 'control-label']) !!}
    {!! Form::text('phone', (old('phone')) ? old('phone') : $lead->patient->phone, ['min' => 0, 'maxlength' => 11, 'size' => 4,'class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('phone'))
        <p class="help-block">
            {{ $errors->first('phone') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>
<div class="col-xs-4 form-group">
    {!! Form::label('gender', 'Gender*', ['class' => 'control-label']) !!}
    {!! Form::select('gender', array('' => 'Select a Gender') + Config::get("constants.gender_array"), (old('gender')) ? old('gender') : $lead->patient->gender, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('gender'))
        <p class="help-block">
            {{ $errors->first('gender') }}
        </p>
    @endif
</div>
<div class="col-xs-4 form-group">
    {!! Form::label('city_id', 'City*', ['class' => 'control-label']) !!}
    {!! Form::select('city_id',$cities, old('city_id'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('city_id'))
        <p class="help-block">
            {{ $errors->first('city_id') }}
        </p>
    @endif
</div>
<div class="col-xs-4 form-group">
    {!! Form::label('lead_source_id', 'Lead Source*', ['class' => 'control-label']) !!}
    {!! Form::select('lead_source_id',$lead_sources, old('lead_source_id'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('lead_source_id'))
        <p class="help-block">
            {{ $errors->first('lead_source_id') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>
<div class="col-xs-4 form-group">
    {!! Form::label('lead_status_id', 'Lead Status*', ['class' => 'control-label']) !!}
    {!! Form::select('lead_status_id',$lead_statuses, old('lead_status_id'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('lead_status_id'))
        <p class="help-block">
            {{ $errors->first('lead_status_id') }}
        </p>
    @endif
</div>
<div class="clearfix"></div>