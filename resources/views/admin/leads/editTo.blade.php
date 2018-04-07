<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">@lang('global.app_edit')</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        {!! Form::model($lead, ['method' => 'PUT', 'id' => 'form-validation', 'route' => ['admin.leads.update', $lead->id]]) !!}
        <div class="form-body">
            <!-- Starts Form Validation Messages -->
        @include('partials.messages')
        <!-- Ends Form Validation Messages -->

            @include('admin.leads.fields')
        </div>
        <div class="form-actions">
            {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
        </div>
        {!! Form::close() !!}
    </div>
</div>
@section('javascript')
    <script src="{{ url('metronic/assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('metronic/assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('js/admin/leads/fields.js') }}" type="text/javascript"></script>
@endsection
