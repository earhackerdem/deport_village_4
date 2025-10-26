<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateTaskRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pendiente,en progreso,completada',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $currentStatus = $this->route('task')->status;
            $newStatus = $this->input('status');
            
            if ($newStatus && !$this->isValidTransition($currentStatus, $newStatus)) {
                $validator->errors()->add('status', 'Transición de status inválida');
            }
        });
    }

    /**
     * Check if the status transition is valid.
     */
    private function isValidTransition(string $from, string $to): bool
    {
        $validTransitions = [
            'pendiente' => ['en progreso', 'completada'],
            'en progreso' => ['completada'],
            'completada' => [] // No se puede cambiar desde completada
        ];
        
        return in_array($to, $validTransitions[$from] ?? []);
    }
}
