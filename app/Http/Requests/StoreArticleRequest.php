<?php

namespace App\Http\Requests;

use App\Enums\ArticleSubmissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // hasVerifiedEmail() disabled — email verification off
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'submission_type' => ['required', Rule::enum(ArticleSubmissionType::class)],
            'url' => ['nullable', 'required_if:submission_type,'.ArticleSubmissionType::Url->value, 'url', 'max:2048'],
            'content' => ['nullable', 'required_if:submission_type,'.ArticleSubmissionType::Text->value, 'string', 'min:40'],
            'title' => [
                'nullable',
                'required_if:submission_type,'.ArticleSubmissionType::Url->value,
                'string',
                'min:10',
                'max:500',
            ],
            'category' => ['nullable', 'string', 'max:120'],
        ];
    }
}
