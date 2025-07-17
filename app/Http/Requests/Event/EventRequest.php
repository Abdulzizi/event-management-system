<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EventRequest extends FormRequest
{
    public $validator;

    public function attributes()
    {
        return [
            'name' => 'Kolom Nama',
            'description' => 'Kolom Deskripsi',
            'start_date' => 'Kolom Tanggal Mulai',
            'end_date' => 'Kolom Tanggal Selesai',
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
            'name' => 'required|max:100',
            'description' => 'nullable|max:255',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    private function updateRules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'name' => 'nullable|max:100',
            'description' => 'nullable|max:255',
            'start_date' => 'nullable|date|before:end_date',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }
}