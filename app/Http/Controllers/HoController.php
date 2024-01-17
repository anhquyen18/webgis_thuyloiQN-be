<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HoController extends Controller
{
    public function index()
    {
        $bigLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 1)->get();
        $mediumLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 2)->get();
        $smallLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 3)->get();
        return response()->json(compact('bigLake', 'mediumLake', 'smallLake'));
        // return response()->json(compact('userData', 'geoserverAccount'));
    }
}
