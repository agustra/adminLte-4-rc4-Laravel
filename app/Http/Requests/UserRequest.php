<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $action = $this->isMethod('POST') ? 'create' : 'edit';

        return $this->user()->can($action.' users', 'web');
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $userId = $isUpdate ? $this->route('id') : null;

        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                $isUpdate ? "unique:users,email,{$userId}" : 'unique:users,email',
            ],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                Password::min(8)->mixedCase()->numbers()->symbols(),
                'confirmed',
            ],
            'old_password' => $isUpdate ? ['nullable', 'required_with:password', 'string'] : [],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['numeric', 'exists:roles,id'],
            'avatar' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama user wajib diisi',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Kata sandi wajib diisi',
            'old_password.required_with' => 'Kata sandi lama wajib diisi jika ingin mengubah kata sandi',
            'roles.required' => 'Role wajib dipilih',
            'roles.min' => 'Minimal satu role harus dipilih',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'roles' => array_map('intval', (array) $this->roles),
        ]);
    }
}
