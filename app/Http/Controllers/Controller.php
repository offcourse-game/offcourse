<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 *  Controller from Laravel to make auth possible, DO NOT CHANGE! --psz
 */
class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
