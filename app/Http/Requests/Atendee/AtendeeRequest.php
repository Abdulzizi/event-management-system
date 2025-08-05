<?php

namespace App\Http\Requests\Atendee;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AtendeeRequest extends FormRequest
{
    public $validator;

    public function attributes()
    {
        return [
            'user_id' => 'ID User',
            'event_id' => 'ID Event',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
        ];
    }

    private function updateRules(): array
    {
        return [
            'user_id' => 'sometimes|exists:users,id',
            'event_id' => 'sometimes|exists:events,id',
        ];
    }
}