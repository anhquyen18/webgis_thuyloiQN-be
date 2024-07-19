<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'newPass' => [
                'required',
                'string',
                'min:10',
                'regex:/[A-Z]/', // Ít nhất một ký tự in hoa
                'regex:/[0-9@$!%*?&]/', // Ít nhất một ký tự đặc biệt hoặc chữ số
            ],
            'checkPass' => 'required|same:newPass',
        ];
    }

    public function messages()
    {
        return [
            'newPass.required' => 'Vui lòng nhập mật khẩu.',
            'newPass.min' => 'Mật khẩu phải chứa ít nhất 10 ký tự.',
            'newPass.regex' => 'Mật khẩu phải chứa ít nhất một ký tự in hoa và ít nhất một ký tự đặc biệt hoặc một chữ số.',
            'checkPass.required' => 'Vui lòng xác nhận mật khẩu',
            'checkPass.same' => 'Mật khẩu xác nhận không khớp',
        ];
    }
}
