@extends('layouts.app')

@section('content')
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Dashboard
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-list font-green-sharp"></i>
                <span class="caption-subject font-green-sharp bold uppercase">Dashbaord</span>
            </div>
            <div class="actions"></div>
        </div>
        <div class="portlet-body">
            @if(Gate::allows('users_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Users</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-green icon-user"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="7,644">{{ number_format($report['users']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            @if(Gate::allows('leads_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Leads</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-red icon-briefcase"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="1,293">{{ number_format($report['leads']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            @if(Gate::allows('appointments_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Appointments</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-purple icon-clock"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="1,293">{{ number_format($report['appointments']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            @if(Gate::allows('cities_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Cities</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-blue icon-settings"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="1,293">{{ number_format($report['cities']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            @if(Gate::allows('locations_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Locations</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-green icon-settings"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="1,293">{{ number_format($report['locations']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            @if(Gate::allows('doctors_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Doctors</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-red icon-settings"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="1,293">{{ number_format($report['doctors']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            @if(Gate::allows('treatments_manage'))
                <div class="col-md-3">
                    <!-- BEGIN WIDGET THUMB -->
                    <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                        <h4 class="widget-thumb-heading">Treatments</h4>
                        <div class="widget-thumb-wrap">
                            <i class="widget-thumb-icon bg-purple icon-settings"></i>
                            <div class="widget-thumb-body">
                                <span class="widget-thumb-subtitle">Total</span>
                                <span class="widget-thumb-body-stat" data-counter="counterup" data-value="1,293">{{ number_format($report['treatments']) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET THUMB -->
                </div>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
@stop