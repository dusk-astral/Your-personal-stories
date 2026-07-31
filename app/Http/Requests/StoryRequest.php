<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'max:100'],
            'body' => ['required', 'max:5000'],
            'tags' => ['required', 'array'],
            'tags.*' => ['required', 'integer', 'exists:tags,id'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048']
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название должно быть обязательным',
            'body.required' => 'Текст должен быть обязательным',
            'tags.required' => 'Выберите хотя бы одну метку',
            'tags.array' => 'Некорректный формат данных',
            'tags.*.exists' => 'Одна из выбранных меток не существует',
            'image_path.max' => 'Изображение не должно превышать 2Mb'
        ];
    }
}
