<?php

namespace App\Http\Requests;

use App\Models\LockedTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class LockedTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $now = Carbon::now()->toDateTimeString();
        return [
            'start_time' => 'required|date|after_or_equal:' . $now,
            'end_time' => 'required|date|after:start_time',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $latestBlockedTime = LockedTime::orderBy('end_time', 'desc')->first();

            if ($latestBlockedTime) {
                $latestEndTime = Carbon::parse($latestBlockedTime->end_time);

                $endTime = Carbon::parse($this->input('end_time'));

                if ($endTime->lessThanOrEqualTo($latestEndTime)) {
                    $validator->errors()->add('end_time', 'Thời gian kết thúc phải lớn hơn thời gian kết thúc của lần khóa mới nhất.');
                }
            }
        });
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'start_time.after_or_equal' => 'Ngày bắt đầu phải lớn hơn hoặc bằng thời gian hiện tại.',
            'end_time.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',
        ];
    }
}
