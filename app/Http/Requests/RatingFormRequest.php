<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => $this->isMethod('post') ? 'required|exists:books,id' : 'sometimes|exists:books,id',

            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ];
    }
    public function attributes(): array
    {
        return [
            'book_id' => 'معرف الكتاب',
            'rating' => 'التقييم',
        ];
        // Custom attribute names used in error messages to be more user-friendly.
    }
    public function messages()
    {
        return [
            // Messages for 'book_id' field
            'book_id.required' => 'حقل :attribute مطلوب.',
            'book_id.exists' => ':attribute المحدد غير موجود.',

            // Messages for 'rating' field
            'rating.required' => 'حقل :attribute مطلوب.',
            'rating.integer' => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
            'rating.min' => 'حقل :attribute لا يمكن أن يكون أقل من 1.',
            'rating.max' => 'حقل :attribute لا يمكن أن يكون أكثر من 5.',
        ];
    }
}
