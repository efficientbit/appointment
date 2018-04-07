@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="page-title col-md-10">@lang('global.appointment_statuses.title')</h1>
        <p class="col-md-2">
            <a href="{{ route('admin.cities.index') }}" class="btn btn-success pull-right">@lang('global.app_back')</a>
        </p>
    </section>
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">@lang('global.app_sort')</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <ul id="data" class="list-group">
                        @foreach($appointment_status as $status)
                            <li class="list-group-item fa fas fa-list-ul orderlist" id="{{$status->id}}">&nbsp;&nbsp;<a>{{$status->name}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(function(){
            $('#data').sortable({
                stop:function(){
                    $.map($(this).find('li'),function(el){

                        var itemID=el.id;
                        var itemIndex=$(el).index();
                        $.ajax({
                            url: route('admin.appointment_statuses.sort_save'),
                            type:'get',
                            dataType:'json',
                            data:{itemID:itemID,itemIndex:itemIndex},

                            success: function($myarray) {
                                if($myarray){
                                    console.log($myarray.status);
                                }
                            }
                        });
                        
                    });
                }
            });
        });
    </script>
@stop

