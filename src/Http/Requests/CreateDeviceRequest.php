<?php

namespace LBF\Devices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LBF\Devices\Models\Device;

class CreateDeviceRequest extends FormRequest
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
            'uuid' => [
                'required',
            ],
            'platform' => [
                'required',
                'platform'
            ]
        ];
    }

    public function messages()
    {
        return [
            'platform.platform' => 'Platform alias does not exist. Use one of: ' . implode(", ", Device::getPlatformAliases()),
        ];
    }
}
