<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Appointments;
use App\Models\Cities;
use App\Models\Doctors;
use App\Models\Leads;
use App\Models\Locations;
use App\Models\Treatments;
use App\User;
use Gate;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $report = array();

        if(Gate::allows('users_manage')) {
            $report['users'] = User::count();
        }
        if(Gate::allows('leads_manage')) {
            $report['leads'] = Leads::count();
        }
        if(Gate::allows('appointments_manage')) {
            $report['appointments'] = Appointments::count();
        }
        if(Gate::allows('cities_manage')) {
            $report['cities'] = Cities::count();
        }
        if(Gate::allows('locations_manage')) {
            $report['locations'] = Locations::count();
        }
        if(Gate::allows('doctors_manage')) {
            $report['doctors'] = Doctors::count();
        }
        if(Gate::allows('treatments_manage')) {
            $report['treatments'] = Treatments::count();
        }

        return view('home', compact('report'));
    }
}
