<div class="modal-header">
    <button type="button" id="closeBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Update Lead Status</h4>
</div>
{!! Form::model($lead, ['method' => 'PUT', 'id' => 'status-validation', 'route' => ['admin.leads.update', $lead->id]]) !!}
<div class="modal-body">
    <div class="form-body">
        <div class="alert alert-danger display-hide"><button class="close" data-close="alert"></button> Please check below. </div>
        <div class="alert alert-success display-hide"><button class="close" data-close="alert"></button> Form is being submit! </div>
        {!! Form::hidden('id', old('id'), ['id' => 'lead']) !!}
        <div class="form-group">
            {!! Form::select('lead_status_id',$lead_statuses, old('lead_status_id'), ['id' => 'lead_status_id', 'class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        </div>
        <div class="form-group">
            {!! Form::textarea('comment','', ['rows' => 3, 'class' => 'form-control', 'placeholder' => 'Type your comment..', 'required' => '']) !!}
        </div>
    </div>
</div>
<div class="modal-footer" id="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
    {!! Form::submit(trans('global.app_save'), ['' => 'lead_status_btn', 'class' => 'btn btn-success']) !!}
</div>
{!! Form::close() !!}
<script src="{{ url('js/admin/leads/lead_status.js') }}" type="text/javascript"></script>
