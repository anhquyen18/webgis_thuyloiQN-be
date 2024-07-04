<?php

namespace App\Services;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class TableSearch
{
    public static function find($model, $searchField, $searchValue, $pageSize)
    {
        // Có thể xử lí dữ liệu trước khi đưa vào tìm kiếm tại đây
        // $results= $model->where($searchField, 'LIKE', "%{$searchValue}%")->paginate(20);
        $results = $model->whereRaw("LOWER({$searchField}) LIKE ?", ['%' . strtolower($searchValue) . '%'])->paginate($pageSize);
        return $results;
    }
}
