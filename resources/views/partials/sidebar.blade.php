@inject('request', 'Illuminate\Http\Request')
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>
            <!-- END SIDEBAR TOGGLER BUTTON -->
            <li class="nav-item start {{ $request->segment(2) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/') }}" class="nav-link ">
                    <i class="icon-home"></i>
                    <span class="title">@lang('global.app_dashboard')</span>
                </a>
            </li>

            @can('users_manage')
                <li class="nav-item start @if(
                    $request->segment(2) == 'permissions' ||
                    $request->segment(2) == 'roles' ||
                    $request->segment(2) == 'users'
                ) active open @endif">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-user"></i>
                        <span class="title">@lang('global.user-management.title')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item start {{ $request->segment(2) == 'permissions' ? 'active ' : '' }}">
                            <a href="{{ route('admin.permissions.index') }}">
                                <span class="title">@lang('global.permissions.title')</span>
                            </a>
                        </li>
                        <li class="nav-item start {{ $request->segment(2) == 'roles' ? 'active active-sub' : '' }}">
                            <a href="{{ route('admin.roles.index') }}">
                                <span class="title">@lang('global.roles.title')</span>
                            </a>
                        </li>
                        <li class="nav-item start {{ $request->segment(2) == 'users' ? 'active active-sub' : '' }}">
                            <a href="{{ route('admin.users.index') }}">
                                <span class="title">@lang('global.users.title')</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @if(Gate::allows('leads_manage'))
                <li class="nav-item start @if($request->segment(2) == 'leads') active open @endif">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-briefcase"></i>
                        <span class="title">@lang('global.leads.title')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item start {{ ($request->segment(2) == 'leads' && $request->segment(3) == 'create') ? 'active' : '' }}">
                            <a href="{{ route('admin.leads.create') }}">
                                <span class="title">Create Lead</span>
                            </a>
                        </li>
                        <li class="nav-item start {{ ($request->segment(2) == 'leads' && $request->segment(3) != 'create' && $request->segment(3) != 'junk') ? 'active' : '' }}">
                            <a href="{{ route('admin.leads.index') }}">
                                <span class="title">@lang('global.leads.title')</span>
                            </a>
                        </li>
                        <li class="nav-item start {{ ($request->segment(2) == 'leads' && $request->segment(3) == 'junk' && $request->segment(3) != 'create') ? 'active' : '' }}">
                            <a href="{{ route('admin.leads.junk') }}">
                                <span class="title">Junk @lang('global.leads.title')</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(Gate::allows('appointments_manage'))
                <li class="nav-item start @if($request->segment(2) == 'appointments') active open @endif">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-clock"></i>
                        <span class="title">@lang('global.appointments.title')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item start {{ ($request->segment(2) == 'appointments' && $request->segment(3) == 'create') ? 'active' : '' }}">
                            <a href="{{ route('admin.appointments.create') }}">
                                <span class="title">Create Appointment</span>
                            </a>
                        </li>
                        <li class="nav-item start {{ ($request->segment(2) == 'appointments' && $request->segment(3) != 'create') ? 'active' : '' }}">
                            <a href="{{ route('admin.appointments.index') }}">
                                <span class="title">@lang('global.appointments.title')</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(
                    Gate::allows('settings_manage') ||
                    Gate::allows('sms_templates_manage') ||
                    Gate::allows('cities_manage') ||
                    Gate::allows('locations_manage') ||
                    Gate::allows('doctors_manage') ||
                    Gate::allows('lead_sources_manage') ||
                    Gate::allows('treatments_manage') ||
                    Gate::allows('lead_statuses_manage') ||
                    Gate::allows('appointment_statuses_manage') ||
                    Gate::allows('cancellation_reasons_manage')
                )
                <li class="nav-item start @if(
                    $request->segment(2) == 'settings' ||
                    $request->segment(2) == 'sms_templates' ||
                    $request->segment(2) == 'cities' ||
                    $request->segment(2) == 'locations' ||
                    $request->segment(2) == 'doctors' ||
                    $request->segment(2) == 'lead_sources' ||
                    $request->segment(2) == 'treatments' ||
                    $request->segment(2) == 'lead_statuses' ||
                    $request->segment(2) == 'appointment_statuses' ||
                    $request->segment(2) == 'cancellation_reasons'
                ) active open @endif">
                    <a href="javascript:;" class="nav-link nav-toggle">
                        <i class="icon-settings"></i>
                        <span class="title">Admin Settings</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        @if(Gate::allows('settings_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.settings.index') }}">
                                    <span class="title">@lang('global.settings.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('sms_templates_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'sms_templates' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.sms_templates.index') }}">
                                    <span class="title">@lang('global.sms_templates.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('cities_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'cities' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.cities.index') }}">
                                    <span class="title">@lang('global.cities.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('locations_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'locations' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.locations.index') }}">
                                    <span class="title">@lang('global.locations.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('doctors_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'doctors' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.doctors.index') }}">
                                    <span class="title">@lang('global.doctors.title')</span>
                                </a>
                            </li>
                        @endif

                        @if(Gate::allows('lead_sources_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'lead_sources' || $request->segment(2) == 'lead_sources_sort' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.lead_sources.index') }}">
                                    <span class="title">@lang('global.lead_sources.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('treatments_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'treatments' || $request->segment(2) == 'treatments_sort' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.treatments.index') }}">
                                    <span class="title">@lang('global.treatments.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('lead_statuses_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'lead_statuses' || $request->segment(2) == 'lead_status_sort' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.lead_statuses.index') }}">
                                    <span class="title">@lang('global.lead_statuses.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('appointment_statuses_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'appointment_statuses' || $request->segment(2) == 'appointment_status_sort' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.appointment_statuses.index') }}">
                                    <span class="title">@lang('global.appointment_statuses.title')</span>
                                </a>
                            </li>
                        @endif
                        @if(Gate::allows('cancellation_reasons_manage'))
                            <li class="nav-item start {{ $request->segment(2) == 'cancellation_reasons' || $request->segment(2) == 'appointment_status_sort' ? 'active active-sub' : '' }}">
                                <a href="{{ route('admin.cancellation_reasons.index') }}">
                                    <span class="title">@lang('global.cancellation_reasons.title')</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>