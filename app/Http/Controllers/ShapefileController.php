<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ShapefileController extends Controller
{
    public function index()
    {
        $lakeSelected = ['ten'];
        function lakeQuery($id)
        {
            $result =  DB::table('ho_thuy_loi')
                ->select(DB::raw("CONCAT('Hồ ', ten) as displayname, ho_chua_quang_nam_epsg5899.name as name, gid, ST_AsGeoJSON(geom, 3) AS geojson,
                 'ho_chua_quang_nam_epsg5899' as layername, phan_loai"))
                ->leftJoin('ho_chua_quang_nam_epsg5899', function ($join) {
                    $join->on(DB::raw("LOWER(REPLACE(UNACCENT(ho_thuy_loi.ten),' ', ''))"), '=', DB::raw("LOWER(REPLACE(ho_chua_quang_nam_epsg5899.name, '_', ''))"));
                })
                ->where('ho_thuy_loi.phan_loai', $id)
                ->orderBy('ho_thuy_loi.id')
                ->get();
            return $result;
        }
        // $bigLake = DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 1)->get();
        $bigLake = lakeQuery(1);
        $mediumLake = lakeQuery(2);
        $smallLake = lakeQuery(3);

        $cuaxaSelected = [
            'ten as name',
            "CONCAT('Đập ', ten) AS displayname",
            'ST_AsGeoJSON(geom, 3) AS geojson',
            "'cuaxa' as layername ",
            'gid',
        ];
        $kenhSelected = [
            "gid::TEXT as name",
            "CONCAT('Kênh ' , gid) AS displayname",
            "ST_AsGeoJSON(geom, 3) AS geojson",
            "'kenh' as layername",
            "gid",
        ];
        $cuaxa = DB::table('cuaxa')->select(DB::raw(implode(",", $cuaxaSelected)))->get();
        $kenh = DB::table('kenh')->select(DB::raw(implode(",", $kenhSelected)))->get();
        return response()->json(compact('bigLake', 'mediumLake', 'smallLake', 'cuaxa', 'kenh'));
        // return response()->json(compact('userData', 'geoserverAccount'));
    }

    public function getFeatureInfo(Request $request)
    {
        $postData = $request->json()->all();
        // return $postData;
        // if ($postData['layer'] = "ho_chua_quang_nam_epsg5899"){
        //     DB::table('ho_thuy_loi')->select('ten')->where('phan_loai', '=', 1)->get();

        // }
        // return $postData;
        switch ($postData['layer']) {
            case 'ho_chua_quang_nam_epsg5899':
                // $lake1 = DB::table($postData['layer'])->where('name', '=', $postData['name'])->get();
                $generalInfo = DB::table('ho_thuy_loi')
                    ->select(DB::raw("ten as \"Tên\", CONCAT('xã ', vi_tri_xa, ', huyện ', vi_tri_huyen) as \"Vị trí\",
                     nam_xd as \"Năm xây dựng\", don_vi_ql as \"Đơn vị quản lý\", co_quy_trinh_vh as \"Quy trình vận hành\", id as \"ID\""))
                    ->whereRaw("LOWER(REPLACE(UNACCENT(ten), ' ', '')) = LOWER(REPLACE(?, '_', ''))", [$postData['name']])
                    ->first();
                if ($generalInfo) {
                    $techInfo1 = DB::table('ho_thuy_loi')
                        ->select(
                            'f_tuoi_tk as Diện tích tưới thiết kế (ha)',
                            'f_tuoi_tt as Diện tích tưới thật tế (ha)',
                            'f_lv as Diện tích lưu vực (km2)',
                            'wmndb as W mndbt (10^6 m3)',
                            'mnc as Mực nước chết (m)',
                            'mndbt as Mực nước dâng bình thường (m)',
                            'mnlkt as Mực nước lũ thiết kế (m)',
                            'so_dap_phu as Số đập phụ',
                            'cao_trinh_dinh_tcs as Cao trình đỉnh tường chắn sóng (m)',
                        )
                        ->where('id', '=', $generalInfo->ID)
                        ->first();
                    $techInfo2 = DB::table('dap_chinh_ho')
                        ->select('cao_trinh_dinh_dap as Cao trình đỉnh đập (m)', 'H_max as H max (m)', 'length as Chiều dài đập (m)')
                        ->where('ho_id', '=', $generalInfo->ID)
                        ->get();

                    $techInfo3 = DB::table('cong_va_tran_ho')
                        ->select(
                            'kich_thuoc_cong as Kích thước cống lấy nước (m)',
                            'hinh_thuc_cong as Hình thức cống lấy nước',
                            'cao_trinh_nguong_tran as Cao trình ngưỡng tràn (m)',
                            'B_tran as B tràn (m)',
                            'hinh_thuc_tran as Hình thức tràn',
                            'co_tran_su_co as Tràn sự cố'
                        )
                        ->where('ho_id', '=', $generalInfo->ID)
                        ->get();

                    return  response()->json(compact('generalInfo', 'techInfo1', 'techInfo2', 'techInfo3'));
                }
                // return response()->json(compact('generalInfo'));
                // return $lake2->toArray();
                break;

            case "cuaxa":
            case "kenh":
                $result = DB::table($postData['layer'])->select('*')->where('gid', '=', $postData['id'])->first();
                if ($result) {
                    return  response()->json($result);
                }

                break;
            default:
                // code to be executed if none of the cases match expression;
        }



        return response()->json(['message' => "Dữ liệu này đang được cập nhật"]);
        // return response()->json($result);
    }

    function vn_to_str($str)
    {
        $unicode = array(

            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

            'd' => 'đ',

            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

            'i' => 'í|ì|ỉ|ĩ|ị',

            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',

            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

            'D' => 'Đ',

            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',

            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

        );

        foreach ($unicode as $nonUnicode => $uni) {

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        // $str = str_replace(' ', '_', $str);
        $str = strtolower($str);
        return $str;
    }

    public function searchFeatureName(Request $request)
    {
        $postData = $request->json()->all();
        $cuaxaSelected = [
            'ten as name',
            "CONCAT('Đập ', ten) AS displayname",
            'ST_AsGeoJSON(geom, 3) AS geojson',
            "'cuaxa' as layername ",
            'gid',
        ];
        $hoChuaSelected = [
            'name',
            "CONCAT('Hồ ' ,ho_chua_quang_nam_epsg5899.name) as displayname",
            "ST_AsGeoJSON(geom, 3) AS geojson",
            "'ho_chua_quang_nam_epsg5899' as layername",
            'gid'
        ];
        $kenhSelected = [
            "gid::TEXT as name",
            "CONCAT('Kênh ' , gid) AS displayname",
            "ST_AsGeoJSON(geom, 3) AS geojson",
            "'kenh' as layername",
            "gid",
        ];


        if ($postData['name']) {
            $result = DB::table(DB::raw("(
                SELECT " . implode(",", $cuaxaSelected) .
                " FROM cuaxa
                UNION ALL
                SELECT  " . implode(",", $hoChuaSelected) .
                " FROM public.ho_chua_quang_nam_epsg5899
                UNION ALL
                SELECT  " . implode(",", $kenhSelected) .
                " FROM kenh
                ) AS search_field"))
                ->whereRaw("LOWER(UNACCENT(displayname)) ilike '%" . $this->vn_to_str($postData['name'])  . "%'")
                ->get();
            return response()->json($result);
        } else {
            return '';
        }
    }

    public function getFeatureByIdAndLayerTitle(Request $request)
    {
        $postData = $request->json()->all();

        return $postData;
    }
}
