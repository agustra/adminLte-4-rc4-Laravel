<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama role wajib diisi',
            'name.unique' => 'Nama role sudah ada',
            'name.max' => 'Nama role maksimal 255 karakter',
            'permissions.*.exists' => 'Permission tidak valid',
        ];
    }
}
