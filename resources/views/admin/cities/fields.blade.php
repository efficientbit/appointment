<div class="form-group col-md-8">
    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
    @if($errors->has('name'))
        <p class="help-block">
            {{ $errors->first('name') }}
        </p>
    @endif
</div>
<div class="form-group col-md-4">
        {!! Form::label('is_featured', 'Make Featured*', ['class' => 'control-label']) !!}
        {!! Form::select('is_featured', array( '' => 'Choose an option', 1 => 'Yes', 0 => 'No'), old('is_featured'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        @if($errors->has('is_featured'))
                <p class="help-block">
                        {{ $errors->first('is_featured') }}
                </p>
        @endif
</div>
<div class="clearfix"></div>