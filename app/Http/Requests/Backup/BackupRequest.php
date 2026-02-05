<?php

namespace App\Http\Requests\Backup;

use Illuminate\Foundation\Http\FormRequest;

class BackupRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'files' => 'required_without:uploads|array|min:1',
            'files.*' => 'file',
            'uploads' => 'required_without:files|array|min:1',
            'uploads.*.path' => 'required|string|max:2048',
            'uploads.*.original_name' => 'required|string|max:255',
            'uploads.*.size' => 'required|integer|min:1',
            'uploads.*.mime_type' => 'required|string|max:255',
            'vault_id' => 'required|exists:vaults,id',
            'folder_id' => 'nullable|exists:folders,id',
            'storage_class' => 'nullable|string|max:100',
        ];
    }
}
