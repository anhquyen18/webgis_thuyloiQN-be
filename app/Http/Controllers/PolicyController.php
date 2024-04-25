<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\Department;
use App\Models\User;

class PolicyController extends Controller
{
    public function index(Request $request)
    {
        $fullAccessOrganizations = $request->attributes->get('full_access_organizations');

        if ($fullAccessOrganizations) {
            return Policy::all();
        } else {
            return Policy::whereNot('id', 2)->get();
        }
    }
}
