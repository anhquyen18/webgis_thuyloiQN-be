<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;

class DepartmentController extends Controller
{
    public function index($organizationId)
    {
        $user = User::find(auth()->user()->id);

        if ($organizationId === 'user-organization-id') {
            return Department::where('organization_id', $user->organization_id)->get();
        } else {

            return Department::where('organization_id', $organizationId)->get();
        }
    }
}
