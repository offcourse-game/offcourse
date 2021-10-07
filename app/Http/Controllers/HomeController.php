<?php

namespace App\Http\Controllers;


/**
 * This Class is generate by Laravel and used for authentication!
 */
class HomeController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('/');
    }
}
