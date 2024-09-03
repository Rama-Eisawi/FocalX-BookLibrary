<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Allow all users to make this request.
        return true;
    }

    //----------------------------------------------------------------------------------------
    /**
     * Prepare the data for validation.
     * This method is called before the validation rules are applied.
     * Here, we trim unnecessary whitespaces from the 'title' and 'author' fields.
     */
    protected function prepareForValidation()
    {
        // Remove leading and trailing whitespaces from 'title' and 'author' fields.
        $this->merge([
            'title' => trim($this->title),
            'author' => trim($this->author),
        ]);
    }

    //----------------------------------------------------------------------------------------
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Check if the request is for updating an existing book
        $isUpdate = $this->route('book') !== null;

        return [
            'title' => [
                $isUpdate ? 'sometimes' : 'required',  // Use 'sometimes' for updates
                'required', // Apply 'sometimes' for updates and 'required' for creation
                'string',
                'between:2,255',
                'regex:/^[A-Za-z0-9\s\-_,\.;:()]+$/',
            ],
            'author' => [
                $isUpdate ? 'sometimes' : 'required',
                'required',
                'string',
                'between:3,255',
                'regex:/^[A-Za-z\s\-\.\_]+$/',
            ],
            'description' => [
                $isUpdate ? 'sometimes' : 'required',
                'required',
                'string',
                'max:1000',
            ],
            'published_at' => [
                $isUpdate ? 'sometimes' : 'required',
                'required',
                'date',
                'before_or_equal:today',
                'after:1971-01-01',
            ],
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
            ApiResponse::error('Validation errors occurred during the update.',
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
            'title' => 'عنوان الكتاب',
            'author' => 'اسم الكاتب',
            'description' => 'وصف الكتاب',
            'published_at' => 'تاريخ النشر',
        ];
        // Custom attribute names used in error messages to be more user-friendly.
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

            // Custom messages for the 'title' field
            'title.required' => 'حقل :attribute مطلوب.',
            'title.string' => 'حقل :attribute يجب أن يكون نصاً صحيحاً.',
            'title.between' => 'حقل :attribute يجب أن يكون بين 2 و 255 حرفاً.',
            'title.regex' => 'حقل :attribute يجب أن يحتوي على حروف، أرقام، ومسافات وبعض علامات الترقيم: -_,.;:()',

            // Custom messages for the 'author' field
            'author.required' => 'حقل :attribute مطلوب.',
            'author.string' => 'حقل :attribute يجب أن يكون نصاً صحيحاً.',
            'author.between' => 'حقل :attribute يجب أن يكون بين 3 و 255 حرفاً.',
            'author.regex' => 'حقل :attribute يجب أن يحتوي فقط على حروف، مسافات، وعلامات الشرطة.',

            // Custom messages for the 'description' field
            'description.required' => 'حقل :attribute مطلوب.',
            'description.string' => 'حقل :attribute يجب أن يكون نصاً صحيحاً.',
            'description.max' => 'حقل :attribute يجب ألا يتجاوز :max حرفاً.',

            // Custom messages for the 'published_at' field
            'published_at.required' => 'حقل :attribute مطلوب.',
            'published_at.date' => 'حقل :attribute يجب أن يكون تاريخاً صحيحاً.',
            'published_at.before_or_equal' => 'حقل :attribute يجب ألا يكون في المستقبل.',
            'published_at.after' => 'حقل :attribute يجب أن يكون بعد 1 يناير 1971.',
        ];
        // Custom error messages for validation rules.
        // ':attribute' will be replaced by the attribute names defined in the 'attributes' method.
    }
}
