<?php
Route::get('/', function () { return redirect('/admin/home'); });

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
$this->post('login', 'Auth\LoginController@login')->name('auth.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', ['uses' => 'HomeController@index', 'as' => 'home']);
    // Permissions Routes
    Route::post('permissions/datatable', ['uses' => 'Admin\PermissionsController@datatable', 'as' => 'permissions.datatable']);
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::post('permissions_mass_destroy', ['uses' => 'Admin\PermissionsController@massDestroy', 'as' => 'permissions.mass_destroy']);

    // Roles Routes
    Route::post('roles/datatable', ['uses' => 'Admin\RolesController@datatable', 'as' => 'roles.datatable']);
    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);

    // Users Routes
    Route::post('users/datatable', ['uses' => 'Admin\UsersController@datatable', 'as' => 'users.datatable']);
    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);

    // Cities Routes
    Route::post('cities/datatable', ['uses' => 'Admin\CitiesController@datatable', 'as' => 'cities.datatable']);
    Route::patch('cities/active/{id}', ['uses' => 'Admin\CitiesController@active', 'as' => 'cities.active']);
    Route::patch('cities/inactive/{id}', ['uses' => 'Admin\CitiesController@inactive', 'as' => 'cities.inactive']);
    Route::resource('cities', 'Admin\CitiesController');
    Route::post('cities_mass_destroy', ['uses' => 'Admin\CitiesController@massDestroy', 'as' => 'cities.mass_destroy']);
    Route::get('cities_sort', ['uses' => 'Admin\CitiesController@sortorder', 'as' => 'cities.sort']);
    Route::get('cities_sort_save', ['uses' => 'Admin\CitiesController@sortorder_save', 'as' => 'cities.sort_save']);

    // Locations
    Route::post('locations/datatable', ['uses' => 'Admin\LocationsController@datatable', 'as' => 'locations.datatable']);
    Route::patch('locations/active/{id}', ['uses' => 'Admin\LocationsController@active', 'as' => 'locations.active']);
    Route::patch('locations/inactive/{id}', ['uses' => 'Admin\LocationsController@inactive', 'as' => 'locations.inactive']);
    Route::resource('locations', 'Admin\LocationsController');
    Route::post('locations_mass_destroy', ['uses' => 'Admin\LocationsController@massDestroy', 'as' => 'locations.mass_destroy']);

    // Doctors
    Route::post('doctors/datatable', ['uses' => 'Admin\DoctorsController@datatable', 'as' => 'doctors.datatable']);
    Route::patch('doctors/active/{id}', ['uses' => 'Admin\DoctorsController@active', 'as' => 'doctors.active']);
    Route::patch('doctors/inactive/{id}', ['uses' => 'Admin\DoctorsController@inactive', 'as' => 'doctors.inactive']);
    Route::resource('doctors', 'Admin\DoctorsController');
    Route::post('doctors_mass_destroy', ['uses' => 'Admin\DoctorsController@massDestroy', 'as' => 'doctors.mass_destroy']);

    // Appointment Statuses
    Route::post('appointment_statuses/datatable', ['uses' => 'Admin\AppointmentStatusesController@datatable', 'as' => 'appointment_statuses.datatable']);
    Route::patch('appointment_statuses/active/{id}', ['uses' => 'Admin\AppointmentStatusesController@active', 'as' => 'appointment_statuses.active']);
    Route::patch('appointment_statuses/inactive/{id}', ['uses' => 'Admin\AppointmentStatusesController@inactive', 'as' => 'appointment_statuses.inactive']);
    Route::resource('appointment_statuses', 'Admin\AppointmentStatusesController');
    Route::post('appointment_statuses_mass_destroy', ['uses' => 'Admin\AppointmentStatusesController@massDestroy', 'as' => 'appointment_statuses.mass_destroy']);
    Route::get('appointment_status_sort', ['uses' => 'Admin\AppointmentStatusesController@sortorder', 'as' => 'appointment_statuses.sort']);
    Route::get('appointment_status_sort_save', ['uses' => 'Admin\AppointmentStatusesController@sortorder_save', 'as' => 'appointment_statuses.sort_save']);

    // Appointments
    Route::get('appointments/doctors', ['uses' => 'Admin\AppointmentsController@loadDoctors', 'as' => 'appointments.doctors']);
    Route::put('appointments/save_doctor', ['uses' => 'Admin\AppointmentsController@saveDoctor', 'as' => 'appointments.save_doctor']);
    Route::get('appointments/treatments', ['uses' => 'Admin\AppointmentsController@loadTreatments', 'as' => 'appointments.treatments']);
    Route::put('appointments/save_treatment', ['uses' => 'Admin\AppointmentsController@saveTreatment', 'as' => 'appointments.save_treatment']);
    Route::get('appointments/showappointmentstatus', ['uses' => 'Admin\AppointmentsController@showAppointmentStatuses', 'as' => 'appointments.showappointmentstatus']);
    Route::put('appointments/storeappointmentstatus', ['uses' => 'Admin\AppointmentsController@storeAppointmentStatuses', 'as' => 'appointments.storeappointmentstatus']);
    Route::get('appointments/detail/{id}', ['uses' => 'Admin\AppointmentsController@detail', 'as' => 'appointments.detail']);
    Route::patch('appointments/send_sms/{id}', ['uses' => 'Admin\AppointmentsController@send_sms', 'as' => 'appointments.send_sms']);
    Route::patch('appointments/active/{id}', ['uses' => 'Admin\AppointmentsController@active', 'as' => 'appointments.active']);
    Route::patch('appointments/inactive/{id}', ['uses' => 'Admin\AppointmentsController@inactive', 'as' => 'appointments.inactive']);
    Route::post('appointments/loadlead', ['uses' => 'Admin\AppointmentsController@loadLeadData', 'as' => 'appointments.load_lead']);
    Route::post('appointments/datatable', ['uses' => 'Admin\AppointmentsController@datatable', 'as' => 'appointments.datatable']);
    Route::resource('appointments', 'Admin\AppointmentsController');
    Route::post('appointments_mass_destroy', ['uses' => 'Admin\AppointmentsController@massDestroy', 'as' => 'appointments.mass_destroy']);

    // Lead Sources
    Route::post('lead_sources/datatable', ['uses' => 'Admin\LeadSourcesController@datatable', 'as' => 'lead_sources.datatable']);
    Route::patch('lead_sources/active/{id}', ['uses' => 'Admin\LeadSourcesController@active', 'as' => 'lead_sources.active']);
    Route::patch('lead_sources/inactive/{id}', ['uses' => 'Admin\LeadSourcesController@inactive', 'as' => 'lead_sources.inactive']);
    Route::resource('lead_sources', 'Admin\LeadSourcesController');
    Route::post('lead_sources_mass_destroy', ['uses' => 'Admin\LeadSourcesController@massDestroy', 'as' => 'lead_sources.mass_destroy']);
    Route::get('lead_sources_sort', ['uses' => 'Admin\LeadSourcesController@sortorder', 'as' => 'lead_sources.sort']);
    Route::get('lead_sources_sort_save', ['uses' => 'Admin\LeadSourcesController@sortorder_save', 'as' => 'lead_sources.sort_save']);

    // Treatments
    Route::post('treatments/datatable', ['uses' => 'Admin\TreatmentsController@datatable', 'as' => 'treatments.datatable']);
    Route::patch('treatments/active/{id}', ['uses' => 'Admin\TreatmentsController@active', 'as' => 'treatments.active']);
    Route::patch('treatments/inactive/{id}', ['uses' => 'Admin\TreatmentsController@inactive', 'as' => 'treatments.inactive']);
    Route::resource('treatments', 'Admin\TreatmentsController');
    Route::post('treatments_mass_destroy', ['uses' => 'Admin\TreatmentsController@massDestroy', 'as' => 'treatments.mass_destroy']);
    Route::get('treatments_sort', ['uses' => 'Admin\TreatmentsController@sortorder', 'as' => 'treatments.sort']);
    Route::get('treatments_sort_save', ['uses' => 'Admin\TreatmentsController@sortorder_save', 'as' => 'treatments.sort_save']);

    // Lead Statuses
    Route::post('lead_statuses/datatable', ['uses' => 'Admin\LeadStatusesController@datatable', 'as' => 'lead_statuses.datatable']);
    Route::patch('lead_statuses/active/{id}', ['uses' => 'Admin\LeadStatusesController@active', 'as' => 'lead_statuses.active']);
    Route::patch('lead_statuses/inactive/{id}', ['uses' => 'Admin\LeadStatusesController@inactive', 'as' => 'lead_statuses.inactive']);
    Route::resource('lead_statuses', 'Admin\LeadStatusesController');
    Route::post('lead_statuses_mass_destroy', ['uses' => 'Admin\LeadStatusesController@massDestroy', 'as' => 'lead_statuses.mass_destroy']);
    Route::get('lead_status_sort', ['uses' => 'Admin\LeadStatusesController@sortorder', 'as' => 'lead_statuses.sort']);
    Route::get('lead_status_sort_save', ['uses' => 'Admin\LeadStatusesController@sortorder_save', 'as' => 'lead_statuses.sort_save']);



    // Leads
    Route::get('leads/junk', ['uses' => 'Admin\LeadsController@junk', 'as' => 'leads.junk']);
    Route::post('leads/junk_datatable', ['uses' => 'Admin\LeadsController@junkDatatable', 'as' => 'leads.junk_datatable']);


    Route::get('leads/showleadstatus', ['uses' => 'Admin\LeadsController@showLeadStatuses', 'as' => 'leads.showleadstatus']);
    Route::put('leads/storeleadstatus', ['uses' => 'Admin\LeadsController@storeLeadStatuses', 'as' => 'leads.storeleadstatus']);
    Route::patch('leads/send_sms/{id}', ['uses' => 'Admin\LeadsController@send_sms', 'as' => 'leads.send_sms']);
    Route::patch('leads/active/{id}', ['uses' => 'Admin\LeadsController@active', 'as' => 'leads.active']);
    Route::patch('leads/inactive/{id}', ['uses' => 'Admin\LeadsController@inactive', 'as' => 'leads.inactive']);
    Route::get('leads/detail/{id}', ['uses' => 'Admin\LeadsController@detail', 'as' => 'leads.detail']);
    Route::get('LeadCommentStore',['uses'=>'Admin\LeadsController@LeadStoreComment','as'=>'leads.storecomment']);
    Route::get('LeadEditDetail',['uses'=>'Admin\LeadsController@LeadEditDetailAjax','as'=>'leads.LeadEditDetail']);
    //Lead Import
    Route::get('leads/import', ['uses' => 'Admin\LeadsController@importLeads', 'as' => 'leads.import']);
    Route::post('leads/upload', ['uses' => 'Admin\LeadsController@uploadLeads', 'as' => 'leads.upload']);
    Route::post('leads/datatable', ['uses' => 'Admin\LeadsController@datatable', 'as' => 'leads.datatable']);
    Route::resource('leads', 'Admin\LeadsController');
    Route::post('leads_mass_destroy', ['uses' => 'Admin\LeadsController@massDestroy', 'as' => 'leads.mass_destroy']);
    Route::post('leads/comment_store', ['uses' => 'Admin\LeadsController@comment_store', 'as' => 'leads.comment_store']);
    // Load and Save Lead Statuses
    Route::get('leads_lead_statuses', ['uses' => 'Admin\LeadsController@loadLeadStatuses', 'as' => 'leads.lead_statuses']);
    Route::put('leads_save_status', ['uses' => 'Admin\LeadsController@saveLeadStatus', 'as' => 'leads.save_status']);
    // Load and Save Lead Sources
    Route::get('leads_lead_sources', ['uses' => 'Admin\LeadsController@loadLeadSources', 'as' => 'leads.lead_sources']);
    Route::put('leads_save_source', ['uses' => 'Admin\LeadsController@saveLeadSource', 'as' => 'leads.save_source']);
    // Load and Save Cities
    Route::get('leads_cities', ['uses' => 'Admin\LeadsController@loadCities', 'as' => 'leads.cities']);
    Route::put('leads_save_city', ['uses' => 'Admin\LeadsController@saveCity', 'as' => 'leads.save_city']);

    // Settings
    Route::post('settings/datatable', ['uses' => 'Admin\SettingsController@datatable', 'as' => 'settings.datatable']);
    Route::patch('settings/active/{id}', ['uses' => 'Admin\SettingsController@active', 'as' => 'settings.active']);
    Route::patch('settings/inactive/{id}', ['uses' => 'Admin\SettingsController@inactive', 'as' => 'settings.inactive']);
    Route::resource('settings', 'Admin\SettingsController');
    Route::post('settings_mass_destroy', ['uses' => 'Admin\SettingsController@massDestroy', 'as' => 'settings.mass_destroy']);

    // SMS Templates
    Route::post('sms_templates/datatable', ['uses' => 'Admin\SMSTemplatesController@datatable', 'as' => 'sms_templates.datatable']);
    Route::patch('sms_templates/active/{id}', ['uses' => 'Admin\SMSTemplatesController@active', 'as' => 'sms_templates.active']);
    Route::patch('sms_templates/inactive/{id}', ['uses' => 'Admin\SMSTemplatesController@inactive', 'as' => 'sms_templates.inactive']);
    Route::resource('sms_templates', 'Admin\SMSTemplatesController');
    Route::post('sms_templates_mass_destroy', ['uses' => 'Admin\SMSTemplatesController@massDestroy', 'as' => 'sms_templates.mass_destroy']);

    // Cancellation Reasons
    Route::post('cancellation_reasons/datatable', ['uses' => 'Admin\CancellationReasonsController@datatable', 'as' => 'cancellation_reasons.datatable']);
    Route::patch('cancellation_reasons/active/{id}', ['uses' => 'Admin\CancellationReasonsController@active', 'as' => 'cancellation_reasons.active']);
    Route::patch('cancellation_reasons/inactive/{id}', ['uses' => 'Admin\CancellationReasonsController@inactive', 'as' => 'cancellation_reasons.inactive']);
    Route::resource('cancellation_reasons', 'Admin\CancellationReasonsController');
    Route::post('cancellation_reasons_mass_destroy', ['uses' => 'Admin\CancellationReasonsController@massDestroy', 'as' => 'cancellation_reasons.mass_destroy']);
    Route::get('cancellation_reasons_sort', ['uses' => 'Admin\CancellationReasonsController@sortorder', 'as' => 'cancellation_reasons.sort']);
    Route::get('cancellation_reasons_sort_save', ['uses' => 'Admin\CancellationReasonsController@sortorder_save', 'as' => 'cancellation_reasons.sort_save']);

});
