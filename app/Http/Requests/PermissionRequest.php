<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Ambil id dari route kalau ada (update), kalau tidak ada berarti store
        $roleId = $this->route('id') ?? $this->route()->parameter('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($roleId),
            ],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama permission wajib diisi',
            'name.unique' => 'Nama permission sudah ada',
            'name.max' => 'Nama permission maksimal 255 karakter',
            'roles.*.exists' => 'Role tidak valid',
        ];
    }
}
