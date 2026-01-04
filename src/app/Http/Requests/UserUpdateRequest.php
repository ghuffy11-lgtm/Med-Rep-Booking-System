<?php

namespace App\Http\Requests;

use App\Services\ValidationRuleService;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isSuperAdmin();
    }

    public function rules()
    {
        return ValidationRuleService::userRules($this->route('user')->id);
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
