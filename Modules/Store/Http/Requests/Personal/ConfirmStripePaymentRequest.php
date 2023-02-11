<?php

namespace Modules\Store\Http\Requests\Personal;

use App\Http\Requests\FormRequest;

class ConfirmStripePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference' => ['required', 'string', 'exists:payments,reference'],
        ];
    }

    public function reference(): string
    {
        return $this->validated()['reference'];
    }
}
