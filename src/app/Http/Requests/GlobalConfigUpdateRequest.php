<?php

namespace App\Http\Requests;

use App\Services\ValidationRuleService;
use Illuminate\Foundation\Http\FormRequest;

class GlobalConfigUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isSuperAdmin();
    }

    public function rules()
    {
        return ValidationRuleService::globalConfigRules();
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
