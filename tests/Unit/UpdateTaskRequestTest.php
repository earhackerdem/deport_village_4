<?php

namespace Tests\Unit;

use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_request_allows_partial_updates(): void
    {
        // Arrange
        $request = new UpdateTaskRequest();
        $data = [
            'title' => 'Updated Task Title'
            // No description, no status - solo title
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertFalse($validator->fails());
    }

    public function test_update_request_validates_status_if_provided(): void
    {
        // Arrange
        $request = new UpdateTaskRequest();
        $data = [
            'title' => 'Updated Task',
            'status' => 'invalid_status'
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_update_request_validates_title_if_provided(): void
    {
        // Arrange
        $request = new UpdateTaskRequest();
        $data = [
            'title' => str_repeat('a', 256), // Más de 255 caracteres
            'description' => 'Updated description'
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_update_request_accepts_valid_status_values(): void
    {
        // Arrange
        $request = new UpdateTaskRequest();
        $validStatuses = ['pendiente', 'en progreso', 'completada'];
        
        foreach ($validStatuses as $status) {
            $data = [
                'title' => 'Updated Task',
                'status' => $status
            ];
            
            // Act
            $validator = Validator::make($data, $request->rules());
            
            // Assert
            $this->assertFalse($validator->fails(), "Status '$status' should be valid");
        }
    }

    public function test_update_request_allows_empty_data(): void
    {
        // Arrange
        $request = new UpdateTaskRequest();
        $data = [];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertFalse($validator->fails());
    }

    public function test_update_request_authorize_returns_true(): void
    {
        // Arrange
        $request = new UpdateTaskRequest();
        
        // Act & Assert
        $this->assertTrue($request->authorize());
    }
}
