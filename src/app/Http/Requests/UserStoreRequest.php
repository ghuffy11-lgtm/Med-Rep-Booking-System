<?php

namespace App\Http\Requests;

use App\Services\ValidationRuleService;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isSuperAdmin();
    }

    public function rules()
    {
        return ValidationRuleService::userRules();
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
