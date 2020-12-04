<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testMethod(){
        return response()->json([
            'msg'=>'It is working'
        ],422);
        //return view('welcome');
    }
}
