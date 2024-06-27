<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSignupRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255',
            'password' => [
                'required',
                'string',
                'min:10',
                'regex:/[A-Z]/', // Ít nhất một ký tự in hoa
                'regex:/[0-9@$!%*?&]/', // Ít nhất một ký tự đặc biệt hoặc chữ số
            ],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Vui lòng nhập tên người dùng.',
            'username.string' => 'Tên người dùng phải là một chuỗi ký tự.',
            'username.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
            'username.unique' => 'Tên người dùng đã tồn tại.',

            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất một ký tự in hoa và ít nhất một ký tự đặc biệt hoặc một chữ số.',
        ];
    }
}
