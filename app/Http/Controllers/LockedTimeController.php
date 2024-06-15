<?php

namespace App\Http\Controllers;

use App\Http\Requests\LockedTimeRequest;
use App\Models\LockedTime;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class LockedTimeController extends Controller
{
    public function create(LockedTimeRequest $request)
    {

        try {
            // Dữ liệu gửi lên được parse thành GMT+0
            // Dữ liệu được ghi vào db với GMT+0
            // Ở đây chỉ hiển thị khác nhau ở db, Carbon sẽ lưu đúng múi GMT+7 còn thời gian được gửi lên từ client thì lưu vào GMT+0
            // Sau đó khi get từ db về thì tất cả đều có múi giờ gmt+0

            // Lúc tạo lockedTime thì vẫn là GMT+7 do có đuôi khác, lưu vào DB lấy ra lại thì mất cái đuôi thành GMT+0

            //"2024-06-15T16:52:13.548000Z"
            // Lúc được tạo thì có trả về đuôi .548000Z, nhưng sau khi lưu vào database và lấy ra lại thì không còn đuôi nữa
            // Không có gì thay đổi cho dù đã thêm timezone cho kiểu dữ liệu

            // Client -> api bắt và tự động trừ lui 7 giờ, gửi về lockedTime tại đây luôn thì vẫn đúng giờ do có múi giờ
            // -> lưu vào db thì lưu số giờ đã bị trờ và múi giờ +7 -> request trả về client là giờ đã bị trừ và múi giờ

            // Vậy khi bắt đầu từ client gửi lên và request trả về thì đã mất 7h 

            // Đã khắc phục khi đổi format ở client
            // Xử lý format ở client trước chứ gửi lên để nó tự convert thì lung tùng beng
            // Tất cả vấn để ở trên đều là do để nó tự convert datetime khi gửi lên
            $postData = $request->json()->all();
            $startTime =  Carbon::parse($postData['start_time']);
            $endTime =  Carbon::parse($postData['end_time']);
            // return response()->json(['lockedTime' => $startTime, 'message' => $postData['start_time']]);

            $lockedTime = LockedTime::create(['start_time' => $startTime, 'end_time' =>  $endTime]);

            return response()->json(['lockedTime' => $lockedTime, 'message' => 'Yêu cầu thành công.']);
        } catch (Exception $e) {
            return $e;
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
    }

    public function getLockedTime()
    {
        try {
            $latestLockedTime = LockedTime::orderBy('id', 'desc')->first();

            return response()->json(['lockedTime' =>  $latestLockedTime, 'message' => 'Yêu cầu thành công.']);
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
    }

    public function endEarly(LockedTime $lockedTime)
    {
        try {
            $now =  Carbon::now();
            $startTime =  Carbon::parse($lockedTime->startTime);
            // Nếu thời gian khoá đã đến, giữ lại start_time
            if ($startTime->greaterThanOrEqualTo($now)) {
                $time = $lockedTime->update(['start_time' => $now, 'end_time' => $now]);
            } else {
                $time = $lockedTime->update(['end_time' => $now]);
            }

            return response()->json(['message' => 'Yêu cầu thành công.']);
        } catch (Exception $e) {
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
    }
}
