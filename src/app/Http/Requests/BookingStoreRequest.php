<?php

namespace App\Http\Requests;

use App\Services\ValidationRuleService;
use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() 
            && $this->user()->isRepresentative() 
            && $this->user()->is_active;
    }

    public function rules()
    {
        return ValidationRuleService::bookingStoreRules();
    }

    public function messages()
    {
        return ValidationRuleService::customMessages();
    }

    public function attributes()
    {
        return ValidationRuleService::customAttributes();
    }
}
