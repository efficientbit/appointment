<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateLeadsRequest extends FormRequest
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
            'full_name' => 'required',
            'email' => 'sometimes|nullable|email',
            'phone' => 'required|numeric',
            'gender' => 'required|numeric',
            'city_id' => 'required|numeric',
            'lead_source_id' => 'required|numeric',
            'lead_status_id' => 'required|numeric',
        ];
    }
}
