<?php

namespace App\Services;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function logActivity($action, $model)
    {
        $user = Auth::user();

        if ($user) {
            UserLog::create([
                'action' => $action,
                'log_id' => $model->id,
                'log_type' => class_basename(get_class($model)),
                'user_id' => $user->id
            ]);
        }
    }
}
