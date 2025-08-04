<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessedRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'file' => 'required|file|mimes:txt,csv,json,xml|max:10240'
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'O arquivo é obrigatório.',
            'file.mimes' => 'Formato de arquivo inválido. Aceito: txt, csv, json, xml.',
            'file.max' => 'O arquivo deve ter no máximo 10MB.'
        ];
    }
}
