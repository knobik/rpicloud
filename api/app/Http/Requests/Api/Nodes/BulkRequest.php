<?php

namespace App\Http\Requests\Api\Nodes;

use Illuminate\Foundation\Http\FormRequest;

class BulkRequest extends FormRequest
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
            'nodeId' => 'required|array',
            'nodeId.*' => 'required|exists:nodes,id'
        ];
    }
}
