<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTaskRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_request_validates_required_title(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        $data = [
            'description' => 'Test description'
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_store_request_validates_title_max_length(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        $data = [
            'title' => str_repeat('a', 256), // Más de 255 caracteres
            'description' => 'Test description'
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_store_request_allows_nullable_description(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        $data = [
            'title' => 'Test Task',
            'description' => null
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertFalse($validator->fails());
    }

    public function test_store_request_validates_status_values(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        $data = [
            'title' => 'Test Task',
            'status' => 'invalid_status'
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_store_request_allows_missing_status(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        $data = [
            'title' => 'Test Task',
            'description' => 'Test description'
        ];
        
        // Act
        $validator = Validator::make($data, $request->rules());
        
        // Assert
        $this->assertFalse($validator->fails());
    }

    public function test_store_request_accepts_valid_status_values(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        $validStatuses = ['pendiente', 'en progreso', 'completada'];
        
        foreach ($validStatuses as $status) {
            $data = [
                'title' => 'Test Task',
                'status' => $status
            ];
            
            // Act
            $validator = Validator::make($data, $request->rules());
            
            // Assert
            $this->assertFalse($validator->fails(), "Status '$status' should be valid");
        }
    }

    public function test_store_request_authorize_returns_true(): void
    {
        // Arrange
        $request = new StoreTaskRequest();
        
        // Act & Assert
        $this->assertTrue($request->authorize());
    }
}
