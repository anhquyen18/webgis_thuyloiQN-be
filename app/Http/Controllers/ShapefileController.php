<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShapefileController extends Controller
{
    public function index()
    {
        $bigLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 1)->get();
        $mediumLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 2)->get();
        $smallLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 3)->get();
        $cuaxa = DB::table('cuaxa')->select('ten', 'gid')->get();
        $kenh = DB::table('kenh')->select('gid')->get();
        return response()->json(compact('bigLake', 'mediumLake', 'smallLake', 'cuaxa', 'kenh'));
        // return response()->json(compact('userData', 'geoserverAccount'));
    }
}
