<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTaskRequestTest extends TestCase
{
    public function test_validation_passes_with_valid_data()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => 'Tarea válida',
            'description' => 'Descripción válida',
            'status' => 'pendiente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_validation_passes_with_minimal_data()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => 'Tarea mínima'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_without_title()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'description' => 'Sin título'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_validation_fails_with_empty_title()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => ''
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_validation_fails_with_title_too_long()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => str_repeat('a', 256)
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_validation_fails_with_invalid_status()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => 'Tarea válida',
            'status' => 'status_inválido'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_validation_passes_with_valid_statuses()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $validStatuses = ['pendiente', 'en progreso', 'completada'];

        foreach ($validStatuses as $status) {
            $data = [
                'title' => 'Tarea válida',
                'status' => $status
            ];

            $validator = Validator::make($data, $rules);

            $this->assertTrue($validator->passes(), "Status '{$status}' should be valid");
        }
    }

    public function test_validation_passes_with_null_description()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => 'Tarea válida',
            'description' => null
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_validation_passes_without_description()
    {
        $request = new StoreTaskRequest();
        $rules = $request->rules();

        $data = [
            'title' => 'Tarea válida'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }
}