<?php

namespace App\Http\Requests;

use App\Services\ValidationRuleService;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules()
    {
        return ValidationRuleService::departmentRules();
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
