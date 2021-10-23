<?php

namespace App\Http\Requests\Api\Nodes;

use App\Models\Node;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BootOrderRequest extends FormRequest
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
            'bootOrder' => 'required|array',
            'bootOrder.*.id' => [
                'required',
                Rule::in(array_keys(Node::bootMapList())),
            ],
        ];
    }
}
