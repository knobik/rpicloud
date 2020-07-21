<?php

namespace App\Http\Requests\Api\Backups;

use Illuminate\Foundation\Http\FormRequest;

class RestoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return [
            'nodeId' => 'required|exists:nodes,id',
            'storageDevice' => 'required',
            'hostname' => 'nullable',
        ];
    }
}
