<?php

namespace App\Http\Requests\Backend\Transport;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ManageUserRequest.
 */
class StoreMainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('行程管理');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transport_start_place'     => ['required', 'max:191'],
            'transport_end_place'  => ['required', 'max:191'],
            'transport_contact_people'    => ['required', 'max:191'],
            'transport_contact_phone' => ['required','cn_phone'],
            'transport_start_time' => ['required'],
            'transport_goods' => ['required']
        ];
    }
}
