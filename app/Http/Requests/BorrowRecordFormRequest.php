<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiResponse;
use App\Models\BorrowRecord;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BorrowRecordFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        //return auth()->check(); // Only authenticated users can borrow books.
    }
    /**
     * Set default values before validation.
     */
    /*protected function prepareForValidation()
    {
        if (!$this->has('borrowed_at')) {
            $this->merge([
                'borrowed_at' => Carbon::now(),
            ]);
        }

        if (!$this->has('due_date')) {
            $this->merge([
                'due_date' => Carbon::now()->addDays(14),
            ]);
        }
    }*/
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'exists:books,id',
                function ($attribute, $value, $fail) {
                    if (BorrowRecord::where('book_id', $value)->whereNull('returned_at')->exists()) {
                        $fail('هذا الكتاب مُعار بالفعل ولم يُعاد بعد');
                    }
                },
            ],

        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // Customize the failed validation response.
        throw new HttpResponseException(
            ApiResponse::error(
                $validator->errors(),
                422
            )
        );
    }
    public function attributes(): array
    {
        return [
            'book_id' => 'معرف الكتاب',
        ];
        // Custom attribute names used in error messages to be more user-friendly.
    }
    public function messages()
    {
        return [
            // Messages for 'book_id' field
            'book_id.required' => 'حقل :attribute مطلوب.',
            'book_id.exists' => 'الكتاب المحدد :attribute غير موجود.',
            'book_id.unique' => 'الكتاب :attribute مُعَار بالفعل ولا يمكن استعارة نفس الكتاب مرة أخرى حتى يتم إرجاعه.',
            /*
            // Messages for 'user_id' field
            'user_id.required' => 'حقل :attribute مطلوب.',
            'user_id.exists' => 'المستخدم المحدد :attribute غير موجود.',


            // Messages for 'borrowed_at' field
            'borrowed_at.required' => 'حقل :attribute مطلوب.',
            'borrowed_at.date' => 'حقل :attribute يجب أن يكون تاريخًا صحيحًا.',
            'borrowed_at.before_or_equal' => 'حقل :attribute يجب أن يكون قبل أو في تاريخ اليوم.',

            // Messages for 'due_date' field
            'due_date.required' => 'حقل :attribute مطلوب.',
            'due_date.date' => 'حقل :attribute يجب أن يكون تاريخًا صحيحًا.',
            'due_date.after' => 'حقل :attribute يجب أن يكون تاريخًا بعد تاريخ الاستعارة.',

            // Messages for 'returned_at' field
            'returned_at.date' => 'حقل :attribute يجب أن يكون تاريخًا صحيحًا.',
            'returned_at.after_or_equal' => 'حقل :attribute لا يمكن أن يكون قبل تاريخ الاستعارة.',*/
        ];
    }
}
