<?php

namespace Modules\Store\Http\Requests\Any;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class DonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules =  [
            'amount' => ['required', 'numeric'],
            'destination' => ['required', 'string'],
            'note' => ['nullable', 'string'],
        ];

        if (!can_get_current_user()) {
            $rules['name'] = ['required', 'string'];
            $rules['email'] = ['required', 'email'];
        }

        return $rules;
    }

    public function amount(): float
    {
        return (float) $this->validated()['amount'];
    }

    public function destination(): string
    {
        return $this->validated()['destination'];
    }

    public function note(): string
    {
        return $this->validated()['note'] ?? '';
    }

    public function name(): string
    {
        return $this->validated()['name'] ?? '';
    }

    public function email(): string
    {
        return $this->validated()['email'] ?? '';
    }
}
