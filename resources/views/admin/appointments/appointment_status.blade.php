<div class="modal-header">
    <button type="button" id="closeBtn" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Update Appointment Status</h4>
</div>
{!! Form::model($appointment, ['method' => 'PUT', 'id' => 'status-validation', 'route' => ['admin.appointments.update', $appointment->app_id]]) !!}
<div class="modal-body">
    <div class="form-body">
        <div class="alert alert-danger display-hide"><button class="close" data-close="alert"></button> Please check below. </div>
        <div class="alert alert-success display-hide"><button class="close" data-close="alert"></button> Form is being submit! </div>
        {!! Form::hidden('id', old('id'), ['id' => 'appointment']) !!}
        {!! Form::hidden('appointment_status_not_show', Config::get('constants.appointment_status_not_show'), ['id' => 'appointment_status_not_show']) !!}
        {!! Form::hidden('cancellation_reason_other_reason', Config::get('constants.cancellation_reason_other_reason'), ['id' => 'cancellation_reason_other_reason']) !!}
        <div class="form-group">
            {!! Form::select('appointment_status_id',$appointment_statuses, old('appointment_status_id'), ['id' => 'appointment_status_id', 'class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        </div>
        <div class="form-group">
            {!! Form::select('cancellation_reason_id',$cancellation_reasons, old('cancellation_reason_id'), ['id' => 'cancellation_reason_id', 'class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
        </div>
        <div class="form-group">
            {!! Form::textarea('reason', old('reason'), ['id' => 'reason', 'rows' => 3, 'class' => 'form-control', 'placeholder' => 'Type your comment..', 'required' => '']) !!}
        </div>
    </div>
</div>
<div class="modal-footer" id="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
    {!! Form::submit(trans('global.app_save'), ['' => 'appointment_status_btn', 'class' => 'btn btn-success']) !!}
</div>
{!! Form::close() !!}
{{--<script src="{{ url('js/admin/appointments/appointment_status.js') }}" type="text/javascript"></script>--}}
<script type="text/javascript">
    var FormValidation = function () {
        var e = function () {
            var e = $("#status-validation"), r = $(".alert-danger", e), i = $(".alert-success", e);
            e.validate({
                errorElement: "span",
                errorClass: "help-block help-block-error",
                focusInvalid: !1,
                ignore:":not(:visible)",
                messages: {
                },
                rules: {
                    appointment_status_id: {required: !0},
                    cancellation_reason_id: {required: !0},
                    reason: {required: !0},
                },
                invalidHandler: function (e, t) {
                    i.hide(), r.show()
                },
                errorPlacement: function (e, r) {
                    var i = $(r).parent(".input-group");
                    i.size() > 0 ? i.after(e) : r.after(e)
                },
                highlight: function (e) {
                    $(e).closest(".form-group").addClass("has-error")
                },
                unhighlight: function (e) {
                    $(e).closest(".form-group").removeClass("has-error")
                },
                success: function (e) {
                    e.closest(".form-group").removeClass("has-error")
                },
                submitHandler: function (e) {
                    i.show(), r.hide();
                    $('#appointment_status_btn').attr('disabled',true);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: route('admin.appointments.storeappointmentstatus'),
                        type: "PUT",
                        data: $("#status-validation").serialize(),
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if(response.status == '1') {
                                $('.alert-success').html("Form is submitted successfully!");
                                $('#modal-footer').remove();
                                $('#appointment' + $('#appointment').val()).html($("#appointment_status_id option:selected").text());
                                setTimeout(function() {
                                    $('#closeBtn').click();
                                }, 1000);
                            } else {
                                $('#appointment_status_btn').removeAttr('disabled');
                            }
                        }
                    });
                    return false;
                }
            })
        }
        return {
            init: function () {
                e()
            }
        }
    }();
    jQuery(document).ready(function () {
        FormValidation.init()
    });
    $(document).ready(function () {
        changeReason('{{ $appointment->appointment_status_id }}');
        $('#appointment_status_id').change(function () {
            changeReason($(this).val());
        });
        $('#cancellation_reason_id').change(function () {
            if($(this).val() != '') {
                changeStatus($(this).val())
            }
        });
    });

    function changeStatus(reason_id) {
        $('#reason').hide();
        if(reason_id != '') {
            if($('#cancellation_reason_other_reason').val() == reason_id) {
                $('#reason').show();
            }
        }
    }

    function changeReason(status_id) {
        $('#reason').hide();
        $('#cancellation_reason_id').hide();
        if(status_id != '') {
            if($('#appointment_status_not_show').val() == status_id) {
                $('#cancellation_reason_id').show();
            }
            var reason_id = $('#cancellation_reason_id').val();
            changeStatus(reason_id);
        }
    }
</script>