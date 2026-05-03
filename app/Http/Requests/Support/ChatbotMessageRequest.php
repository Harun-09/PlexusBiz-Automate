<?php

namespace App\Http\Requests\Support;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:4000'],
            'subject' => ['nullable', 'string', 'max:160'],
            'create_ticket' => ['sometimes', 'boolean'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
        ];
    }
}
