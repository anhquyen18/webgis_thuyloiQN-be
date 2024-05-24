<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class ReservoirSafetyController extends Controller
{
    function getCheckbox($status)
    {
        $checkedBox = '<w:sym w:font="Wingdings" w:char="F0FE"/>';
        $unCheckedBox = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
        return $status ? $checkedBox : $unCheckedBox;
    }

    public function uploadTemporaryImage(Request $request)
    {
        // Mỗi file sẽ được tải lên tạm thời cho đến khi được lưu
        // Nếu lưu sẽ được copy sang folder cụ thể để lưu trữ
        // Nếu ở client huỷ, các file tạm này sẽ được xoá đi
        $user = auth()->user();

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif', // Ảnh có định dạng jpeg, png, jpg hoặc gif
        ]);

        if ($request->has('file')) {
            $file = $request->file('file');
            $fileName = $user->id . '_' . $file->getClientOriginalName();
            $file->move(storage_path('temporary-file-uploaded'),  $fileName);

            return response()->json(['message' => 'Thành công.']);
        } else {
            return response()->json(['message' => 'Thất bại. Vui lòng thử lại sau.'], 500);
        }
    }

    public function deleteTemporaryImage(Request $request)
    {
        $user = auth()->user();
        $postData = $request->json()->all();
        $fileList = $postData['fileList'];
        try {

            foreach ($fileList as $file) {

                if ($file['name']) {
                    $filePath = storage_path('temporary-file-uploaded') . '\\' . $user->id . '_' . $file['name'];
                    // $filePath =  $user->id . '_' . $file['name'];
                    // return response()->json(['message' => $filePath], 200);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                        // Storage::disk('storage/temporary-file-uploaded')->delete($filePath);
                        // return response()->json(['message' => 'Xoá file thành công.']);
                    } else {
                        // return response()->json(['message' => 'Không tìm thấy file.'], 404);
                    }
                } else {
                    // return response()->json(['message' => 'Tên file không có.'], 400);
                }
            }
        } catch (Exception $e) {
            return $e;
            return response()->json(['caution' => $e, 'message' => 'Thất bại. Vui lòng thử lại sau.'], 500);
        }

        return response()->json(['message' => 'Thành công.']);
    }

    public function createSafetyReport(Request $request, $id)
    {
        $user = auth()->user();
        $postData = $request->json()->all();
        $currentDateTime = Carbon::now();
        $currentYear = $currentDateTime->year;
        $currentMonth = $currentDateTime->month;
        $currentDay = $currentDateTime->day;

        $reservoirInfo = array(
            'id' => 1,
            'Tên' => 'Hồ A1',
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
            'Dung tích phòng lũ' => 2505,
            'Mực nước lũ thiết kế' => 50,
            'Mực nước lũ kiểm tra' => 40,
            'Mực nước dâng bình thường' => 5,
            'Mực nước chết' => 5,
            'Mực nước lớn nhất đã xảy ra' => 100,
            'Mực nước thấp nhất' => 10,
            'Đường quản lí' => false,
            'Các loại quan trắc' => false,
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
            'abc' => 1,
        );

        $mainDams =
            [
                array('id' => 1, 'DAM_CREST_LENGTH' => 500, 'MAX_HEIGHT' => 50, 'DAM_CREST_ELEVATION' => 65, 'SEAWALL_TOP_ELEVATION' => 100, 'TYPE' => 'Đập đất'),
                array('id' => 2, 'DAM_CREST_LENGTH' => 420, 'MAX_HEIGHT' => 20, 'DAM_CREST_ELEVATION' => 81, 'SEAWALL_TOP_ELEVATION' => 80, 'TYPE' => 'Đập đất'),
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

        $drainages = [
            array('id' => 1, 'Tên' => 'Tháo nước AQ', 'Chiều dài' => 100),
        ];
        try {
            $image_storage = storage_path('images/safety-report');
            // Copy từ thư mục lưu tạm qua thư mục lưu trữ
            foreach ($postData['fileList'] as $file) {
                if ($file['name']) {
                    $filePath = storage_path('temporary-file-uploaded') . '\\' . $user->id . '_' . $file['name'];
                    if (File::exists($filePath)) {
                        File::copy($filePath, $image_storage . '\\' . $postData['id'] . '_' . $file['name']);
                        File::delete($filePath);
                    } else {
                        // return response()->json(['message' => 'Không tìm thấy file.'], 404);
                    }
                } else {
                    // return response()->json(['message' => 'Tên file không có.'], 400);
                }
            }


            if ($postData['download'] == true) {
                $mainDamBlock = [];
                for ($i = 0; $i < count($mainDams); $i++) {
                    $mainDamBlock[] = [
                        'INDEX' => $i + 1,
                        'NAME' => $mainDams[$i]['id'],
                        'DAM_CREST_LENGTH' => $mainDams[$i]['DAM_CREST_LENGTH'],
                        'MAX_HEIGHT' => $mainDams[$i]['MAX_HEIGHT'],
                        'DAM_CREST_ELEVATION' => $mainDams[$i]['DAM_CREST_ELEVATION'],
                        'SEAWALL_TOP_ELEVATION' => $mainDams[$i]['SEAWALL_TOP_ELEVATION'],
                        'TYPE' => $mainDams[$i]['TYPE'],
                        'NORMAL_CHECK_BOX' => $this->getCheckbox(!$postData['mainDams'][$i]['status']),
                        'ABNORMAL_CHECK_BOX' => $this->getCheckbox($postData['mainDams'][$i]['status']),
                        'DESCRIPTION' => $postData['mainDams'][$i]['description'],
                    ];
                }

                $subDamBlock = [];
                for ($i = 0; $i < count($subDams); $i++) {
                    $subDamBlock[] = [
                        'INDEX' => $i + 1,
                        'NAME' => $i + 1,
                        'LENGTH' => $subDams[$i]['Chiều dài'],
                        'HEIGHT' => $subDams[$i]['Chiều cao'],
                        'NORMAL_CHECK_BOX' => $this->getCheckbox(!$postData['subDams'][$i]['status']),
                        'ABNORMAL_CHECK_BOX' => $this->getCheckbox($postData['subDams'][$i]['status']),
                        'DESCRIPTION' => $postData['subDams'][$i]['description'],
                    ];
                }

                $sewerBlock = [];
                for ($i = 0; $i < count($sewers); $i++) {
                    $sewerBlock[] = [
                        'INDEX' => $i + 1,
                        'NAME' => $sewers[$i]['Tên'],
                        'LENGTH' => $sewers[$i]['Chiều dài'],
                        'NORMAL_CHECK_BOX' => $this->getCheckbox(!$postData['sewers'][$i]['status']),
                        'ABNORMAL_CHECK_BOX' => $this->getCheckbox($postData['sewers'][$i]['status']),
                        'DESCRIPTION' => $postData['sewers'][$i]['description'],
                    ];
                }

                $spillwayBlock = [];
                for ($i = 0; $i < count($spillways); $i++) {
                    $spillwayBlock[] = [
                        'INDEX' => $i + 1,
                        'NAME' => $spillways[$i]['Tên'],
                        'WIDTH' => $spillways[$i]['Chiều rộng tràn'],
                        'NORMAL_CHECK_BOX' => $this->getCheckbox(!$postData['spillways'][$i]['status']),
                        'ABNORMAL_CHECK_BOX' => $this->getCheckbox($postData['spillways'][$i]['status']),
                        'DESCRIPTION' => $postData['spillways'][$i]['description'],
                    ];
                }


                $drainageBlock = [];
                for ($i = 0; $i < count($drainages); $i++) {
                    $drainageBlock[] = [
                        'INDEX' => $i + 1,
                        'NAME' => $drainages[$i]['Tên'],
                        'LENGTH' => $drainages[$i]['Chiều dài'],
                        'NORMAL_CHECK_BOX' => $this->getCheckbox(!$postData['drainages'][$i]['status']),
                        'ABNORMAL_CHECK_BOX' => $this->getCheckbox($postData['drainages'][$i]['status']),
                        'DESCRIPTION' => $postData['drainages'][$i]['description'],
                    ];
                }


                $templateFilePath = storage_path('templates/mau-bao-cao-an-toan-dap.docx');

                // $path=public_path("uploads/files/".$filename);
                $templateProcessor = new TemplateProcessor($templateFilePath);

                $templateProcessor->setValue('DAY', $currentDay);
                $templateProcessor->setValue('MONTH', $currentMonth);
                $templateProcessor->setValue('YEAR', $currentYear);

                $templateProcessor->setValue('RESERVOIR_NAME', $reservoirInfo['Tên']);
                $templateProcessor->setValue('ADDRESS', $reservoirInfo['Địa chỉ']);
                $templateProcessor->setValue('BULDING_YEAR', $reservoirInfo['Năm xây dựng']);
                $templateProcessor->setValue('BULDING_COST', $reservoirInfo['Kinh phí']);
                $templateProcessor->setValue('OWNER_NAME', $reservoirInfo['Tên chủ sở hữu đập']);
                $templateProcessor->setValue('MANAGER_NAME', $reservoirInfo['Tổ chức, cá nhân khai thác']);

                $templateProcessor->setValue('CONSTRUCTION_LEVEL', $reservoirInfo['Cấp công trình']);
                $templateProcessor->setValue('MAIN_AREA', $reservoirInfo['Diện tích lưu vực']);
                $templateProcessor->setValue('MNDBT_CAPCITY', $reservoirInfo['Dung tích MNDBT']);
                $templateProcessor->setValue('USEFUL_CAPCITY', $reservoirInfo['Dung tích hữu ích']);
                $templateProcessor->setValue('DEADLY_CAPCITY', $reservoirInfo['Dung tích chết']);
                $templateProcessor->setValue('FLOOD_PROTECTION_CAPCITY', $reservoirInfo['Dung tích phòng lũ']);
                $templateProcessor->setValue('DESIGNED_FLOOD_LEVEL', $reservoirInfo['Mực nước lũ thiết kế']);
                $templateProcessor->setValue('CHECKED_FLOOD_LEVEL', $reservoirInfo['Mực nước lũ kiểm tra']);
                $templateProcessor->setValue('NOMAL_RISING_LEVEL', $reservoirInfo['Mực nước dâng bình thường']);
                $templateProcessor->setValue('DEADLY_LEVEL', $reservoirInfo['Mực nước chết']);
                $templateProcessor->setValue('HIGHEST_OCCURRED_LEVEL', $reservoirInfo['Mực nước lớn nhất đã xảy ra']);
                $templateProcessor->setValue('LOWEST_LEVEL', $reservoirInfo['Mực nước thấp nhất']);

                $templateProcessor->cloneBlock('MAIN_DAM_BLOCK', 0, true, false, $mainDamBlock);
                $templateProcessor->cloneBlock('SUB_DAM_BLOCK', 0, true, false, $subDamBlock);
                $templateProcessor->cloneBlock('SEWER_BLOCK', 0, true, false, $sewerBlock);
                $templateProcessor->cloneBlock('SPILLWAY_BLOCK', 0, true, false, $spillwayBlock);
                $templateProcessor->cloneBlock('DRAINAGE_BLOCK', 0, true, false, $drainageBlock);


                $templateProcessor->setValue('HAS_STREET_CHECK_BOX_1', $this->getCheckbox($reservoirInfo['Đường quản lí']));
                $templateProcessor->setValue('HAS_STREET_CHECK_BOX_2', $this->getCheckbox(!$reservoirInfo['Đường quản lí']));
                $templateProcessor->setValue('HAS_MONITOR_CHECK_BOX_1', $this->getCheckbox($reservoirInfo['Các loại quan trắc']));
                $templateProcessor->setValue('HAS_MONITOR_CHECK_BOX_2', $this->getCheckbox(!$reservoirInfo['Các loại quan trắc']));

                $templateProcessor->setValue('HAS_OPERATING_SYS_CHECK_BOX_1', $this->getCheckbox($reservoirInfo['Hệ thống giám sát vận hành']));
                $templateProcessor->setValue('HAS_OPERATING_SYS_CHECK_BOX_2', $this->getCheckbox(!$reservoirInfo['Hệ thống giám sát vận hành']));

                $templateProcessor->setValue('HAS_DATABASE_CHECK_BOX_1', $this->getCheckbox($reservoirInfo['Cơ sở dữ liệu hồ chứa']));
                $templateProcessor->setValue('HAS_DATABASE_CHECK_BOX_2', $this->getCheckbox(!$reservoirInfo['Cơ sở dữ liệu hồ chứa']));

                $templateProcessor->setValue('HAS_OPERATING_PROCEDURE_CHECK_BOX_1', $this->getCheckbox($reservoirInfo['Quy trình vận hành']));
                $templateProcessor->setValue('HAS_OPERATING_PROCEDURE_CHECK_BOX_2', $this->getCheckbox(!$reservoirInfo['Quy trình vận hành']));

                // $templateProcessor->setValue('HAS_OPERATING_SYS_CHECK_BOX_1', $this->getCheckbox(false));
                // $templateProcessor->setValue('HAS_OPERATING_SYS_CHECK_BOX_2', $this->getCheckbox(!false));

                // $templateProcessor->setValue('HAS_DATABASE_BOX_1', $this->getCheckbox(false));
                // $templateProcessor->setValue('HAS_DATABASE_BOX_2', $this->getCheckbox(!false));

                // $templateProcessor->setValue('HAS_OPERATOR_PROCEDURE_CHECK_BOX_1', $this->getCheckbox(false));
                // $templateProcessor->setValue('HAS_OPERATOR_PROCEDURE_CHECK_BOX_2', $this->getCheckbox(!false));




                $outputFilePath = storage_path('bao-cao-an-toan-dap-' . rand(10, 10000) . '.docx');

                $templateProcessor->saveAs($outputFilePath);
                $headers = [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                return response()->download($outputFilePath, 'mau-bao-cao.docx', $headers)->deleteFileAfterSend();
            } else {
                return response()->json(['message' => 'Tạo báo cáo thành công.']);
            }
        } catch (Exception $e) {
            return $e;
            // return response()->json(['caution' => $e, 'message' => 'Yêu cầu thất bại.'], 500);
        }
    }
}
