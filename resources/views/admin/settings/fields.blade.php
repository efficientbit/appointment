@if($setting->id == 4)
        <div class="form-group">
                {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                @if($errors->has('name'))
                        <p class="help-block">
                                {{ $errors->first('name') }}
                        </p>
                @endif
        </div>
        <div class="form-group">
                {!! Form::label('data', 'Data*', ['class' => 'control-label']) !!}
                {!! Form::select('data', array(1 => 'Yes', 0 => 'No'), old('data'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                @if($errors->has('data'))
                        <p class="help-block">
                                {{ $errors->first('data') }}
                        </p>
                @endif
        </div>
@else
        <div class="form-group">
                {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                @if($errors->has('name'))
                        <p class="help-block">
                                {{ $errors->first('name') }}
                        </p>
                @endif
        </div>
        <div class="form-group">
                {!! Form::label('data', 'Data*', ['class' => 'control-label']) !!}
                {!! Form::textarea('data', old('data'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                @if($errors->has('data'))
                        <p class="help-block">
                                {{ $errors->first('data') }}
                        </p>
                @endif
        </div>
@endif