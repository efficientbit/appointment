<div class="form-group @if($errors->has('name')) has-error @endif">
    {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '']) !!}
    @if($errors->has('name'))
        <span class="help-block help-block-error">
            {{ $errors->first('name') }}
        </span>
    @endif
</div>