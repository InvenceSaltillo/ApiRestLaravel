<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        echo 'Index de CarController'; die();
    }
}
