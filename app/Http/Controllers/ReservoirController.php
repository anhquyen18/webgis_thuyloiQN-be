<?php

namespace App\Http\Controllers;

use App\Models\Reservoir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;

class ReservoirController extends Controller
{
    public function index()
    {
        try {
            $reservoirs = Reservoir::select(DB::raw("CONCAT('Hồ ', id) as name, id"))->get();

            return response()->json(['message' => 'Yêu cầu thành công.', 'reservoirs' => $reservoirs]);
        } catch (Exception $e) {
            // return $e;
            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
    }

    public function getReservoir($id)
    {
        $reservoirInfo = array(
            'id' => 1,
            'Địa chỉ' => 'xã Tam Ngọc, tp Tam Kỳ, tỉnh Quảng Nam',
            'Năm xây dựng' => '1999-2010',
            'Kinh phí' => '1000 tỷ',
            'Nguồn vốn đầu tư xây dựng' => '',
            'Tên chủ sở hữu đập' => 'Trung tâm nghiên cứu tài nguyên nước', // Chỉ là id và tham chiếu đến các thông tin của chủ sở hữu ở bảng khác
            'Tổ chức, cá nhân khai thác' => 'Trung tâm nghiên cứu tài nguyên nước',
            'Cấp công trình' => '3',
            'Diện tích lưu vực' => 10000,
            'Dung tích MNDBT' => 11242,
            'Dung tích hữu ích' => 24425,
            'Dung tích chết' => 2421,
            'Mực nước lũ thiết kế' => 50,
            'Mực nước lũ kiểm tra' => 40,
            'Mực nước dâng bình thường' => 5,
            'Mực nước chết' => 5,
            'Mực nước lớn nhất đã xảy ra' => 100,
            'Mực nước thấp nhất' => 10,
            'Đường quản lí' => false,
            'Hệ thống giám sát vận hành' => false,
            'Cơ sở dữ liệu hồ chứa' => false,
            'Quy trình vận hành' => false,
            'Phương án bảo vệ' => false,
            'Phương án phó thiên tai' => false,
            'Kiểm định an toàn hồ chứa' => 'testing testing',
            'Lưu trữ hồ sơ hồ chứa nước' => 'Đầy đủ',
            'Thiết bị thông tin cảnh báo' => true,
            'Quy trình vận hành cửa van, quy trình bảo trì công trình' =>  'test 11111',
            'Quá trình quản lí khai khác' => 'test abc',
        );

        // Các dữ liệu này giả lập cho các công trình phụ trợ mà hồ chứa sở hữu

        $mainDams =
            [
                array('id' => 1, 'Chiều dài đỉnh đập' => 500, 'Chiều cao lớn nhất' => 50, 'Cao trình đỉnh đập', 'Cao trình TCS' => 100, 'Loại đập' => 'Đập đất'),
                // array('id' => 2, 'Chiều dài đỉnh đập' => 420, 'Chiều cao lớn nhất' => 20, 'Cao trình đỉnh đập', 'Cao trình TCS' => 80, 'Loại đập' => 'Đập đất'),
            ];

        $subDams = [
            ['id' => 1, 'Chiều dài' => 222, 'Chiều cao' => 50],
            ['id' => 2, 'Chiều dài' => 232, 'Chiều cao' => 50],
        ];

        $sewers =
            [
                array('id' => 1, 'Tên' => 'Cống phía Bắc', 'Chiều dài' => 50),
                array('id' => 2, 'Tên' => 'Cống phía Nam 1',  'Chiều dài' => 50),
                array('id' => 3, 'Tên' => 'Cống phía Nam 2',  'Chiều dài' => 50),
            ];

        $spillways =
            [
                array('id' => 1, 'Tên' => 'Tràn ABCxyz', 'Chiều rộng tràn' => 200),
            ];

        $monitors = [
            array('id' => 1, 'Tên' => 'Hệ thống giám sát 01', 'Chiều dài' => 100),
        ];


        $drainages = [
            array('id' => 1, 'Tên' => 'Tháo nước AQ', 'Chiều dài' => 100),
        ];

        try {
        } catch (Exception $e) {

            return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }

        return response()->json([
            'reservoirInfo' => $reservoirInfo,
            'mainDams' => $mainDams,
            'subDams' => $subDams,
            'sewers' => $sewers,
            'spillways' => $spillways,
            'drainages' => $drainages,
            'monitors' => $monitors,
            'message' => 'Yêu cầu thành công.'
        ]);
    }
}
