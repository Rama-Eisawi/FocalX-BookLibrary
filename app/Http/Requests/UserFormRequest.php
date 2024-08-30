<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    //----------------------------------------------------------------------------------------
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Trim unnecessary whitespaces.
        $this->merge([
            'name' => trim($this->name),
            'email' => trim($this->email),
        ]);
    }
    //----------------------------------------------------------------------------------------
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule,array<mixed>,string>
     */
    public function rules(): array
    {
        // Get the ID of the user being updated, if applicable
        $userId = $this->route('user') !== null ? $this->route('user')->id : null;

        // Check if the request is for updating an existing book
        //$isUpdate = $this->route('user') !== null;

        return [
            'name' => [
                $userId ? 'sometimes' : 'required',  // Use 'sometimes' for updates
                'required',
                'string',
                'between:2,255',
                'regex:/^[A-Za-z\s\-\_]+$/'
            ],
            // The 'name' field is required, must be a string, between 2 and 255 characters long,
            // and can only contain letters, spaces, and hyphens.

            'email' => [
                $userId ? 'sometimes' : 'required',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
                //'unique:users,email'
            ],
            // The 'email' field is required, must be a valid email address, max 255 characters,
            // and must be unique in the 'users' table, email column.

            'password' => [
                $userId ? 'sometimes' : 'required',
                'required',
                'string',
                'min:8', // Minimum length of 8 characters
                'regex:/[a-z]/',     // At least one lowercase letter
                'regex:/[A-Z]/',     // At least one uppercase letter
                'regex:/[0-9]/',     // At least one digit
                'regex:/[@$!%*?&-_]/', // At least one special character
            ],
            // The 'password' field is required, must be a string, and should adhere to Laravel's password rule.
        ];
    }
    //----------------------------------------------------------------------------------------
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Customize the failed validation response.
        throw new HttpResponseException(
            ApiResponse::error(
                [$validator->errors()],
                'Validation errors occurred during the update.',
                422
            )
        );
    }
    //----------------------------------------------------------------------------------------
    /**
     * Customize attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'email' => 'البريد الالكتروني',
            'password' => 'كلمة السر',
        ];
    }
    //----------------------------------------------------------------------------------------
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            // Custom messages for the 'name' field
            'name.required' => 'حقل :attribute مطلوب.',
            'name.string' => 'حقل :attribute يجب أن يكون نصًا صحيحًا.',
            'name.between' => 'حقل :attribute يجب أن يكون بين 2 و 255 حرفًا.',
            'name.regex' => 'حقل :attribute يمكن أن يحتوي فقط على أحرف، مسافات، وشرطات.',

            // Custom messages for the 'email' field
            'email.required' => 'حقل :attribute مطلوب.',
            'email.string' => 'حقل :attribute يجب أن يكون نصًا صحيحًا.',
            'email.email' => 'حقل :attribute يجب أن يكون بريدًا إلكترونيًا صحيحًا.',
            'email.max' => 'حقل :attribute يجب ألا يتجاوز 255 حرفًا.',
            'email.unique' => 'البريد الإلكتروني :attribute مستخدم بالفعل.',

            // Custom messages for the 'password' field
            'password.required' => 'حقل :attribute مطلوب.',
            'password.string' => 'حقل :attribute يجب أن يكون نصًا صحيحًا.',
            'password.min' => 'حقل :attribute يجب أن يكون على الأقل 8 أحرف.',
            'password.regex' => 'حقل :attribute يجب أن يحتوي على حرف صغير واحد على الأقل، وحرف كبير واحد على الأقل، ورقم واحد على الأقل، ورمز خاص واحد على الأقل (@$!%*?&-_).',
        ];
    }
}
