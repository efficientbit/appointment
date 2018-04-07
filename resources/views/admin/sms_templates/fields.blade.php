<div class="col-xs-12 form-group">
        {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
        {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        @if($errors->has('name'))
                <p class="help-block">
                        {{ $errors->first('name') }}
                </p>
        @endif
</div>
<div class="col-xs-8 form-group">
        {!! Form::label('content', 'Content*', ['class' => 'control-label']) !!}
        {!! Form::textarea('content', old('content'), ['id' => 'content', 'class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        @if($errors->has('content'))
                <p class="help-block">
                        {{ $errors->first('content') }}
                </p>
        @endif
</div>
<div class="col-xs-4 form-group">
        {!! Form::label('variable', 'Variables', ['class' => 'control-label']) !!}
        <select class="form-control" id="variable" multiple style="height: 180px;">
            <optgroup label="Appointments">
                <option value="##patient_name##">Patient Name</option>
                <option value="##patient_phone##">Patient Phone</option>
                <option value="##doctor_name##">Doctor Name</option>
                <option value="##doctor_profile_link##">Doctor Profile Link</option>
                <option value="##appointment_date##">Appointment Date</option>
                <option value="##appointment_time##">Appointment Time</option>
                <option value="##fdo_name##">FDO Name</option>
                <option value="##fdo_phone##">FDO Phone</option>
                <option value="##centre_name##">Centre Name</option>
                <option value="##centre_address##">Centre Address</option>
                <option value="##centre_google_map##">Centre Google Map</option>
            </optgroup>
            <optgroup label="Leads">
                <option value="##full_name##">Full Name</option>
                <option value="##email##">Email</option>
                <option value="##phone##">Phone</option>
                <option value="##gender##">Gender</option>
                <option value="##city_name##">City</option>
                <option value="##lead_source_name##">Lead Source</option>
                <option value="##lead_status_name##">Lead Status</option>
            </optgroup>
            <optgroup label="Others">
                <option value="##head_office_phone##">Head Office Phone</option>
            </optgroup>
        </select>
        <button type="button" onclick="applyVariable();" class="btn btn-warning" style="margin-top: 5px;">Apply Variable</button>
</div>
<div class="clearfix"></div>