# Factory Examples

Comprehensive examples for creating realistic test data using Laravel Factories.

## Basic Factory Structure
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pendiente', 'en progreso', 'completada']),
        ];
    }
}
```

---

## Field Type Examples

### Strings
```php
'title' => fake()->sentence(3),
'name' => fake()->name(),
'slug' => fake()->slug(),

'summary' => fake()->text(200),
'excerpt' => fake()->sentence(10),

'description' => fake()->paragraph(),
'content' => fake()->paragraphs(3, true),
'bio' => fake()->text(500),
```

### Numbers
```php
'priority' => fake()->numberBetween(1, 5),
'order' => fake()->randomNumber(2),
'quantity' => fake()->randomDigit(),

'price' => fake()->randomFloat(2, 10, 100),
'rating' => fake()->randomFloat(1, 0, 5),
'percentage' => fake()->randomFloat(2, 0, 100),
```

### Booleans
```php
'is_active' => fake()->boolean(),
'is_featured' => fake()->boolean(25),
'is_published' => fake()->boolean(80),
```

### Dates and Times
```php
'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
'published_at' => fake()->dateTimeBetween('-2 weeks', 'now'),

'due_date' => fake()->dateTimeBetween('now', '+1 month'),
'expires_at' => fake()->dateTimeBetween('+1 week', '+6 months'),

'birth_date' => fake()->dateTimeBetween('-80 years', '-18 years'),
'hired_at' => fake()->dateTimeBetween('-5 years', '-1 day'),

'start_time' => fake()->time(),
```

### Enums / Specific Values
```php
'status' => fake()->randomElement(['pendiente', 'en progreso', 'completada']),
'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
'type' => fake()->randomElement(['bug', 'feature', 'improvement']),

'status' => fake()->randomElement([
    'pendiente' => 50,
    'en progreso' => 30,
    'completada' => 20,
]),
```

### Email and Internet
```php
'email' => fake()->unique()->safeEmail(),
'website' => fake()->url(),
'domain' => fake()->domainName(),
'ip_address' => fake()->ipv4(),
```

### Person Data
```php
'first_name' => fake()->firstName(),
'last_name' => fake()->lastName(),
'full_name' => fake()->name(),
'phone' => fake()->phoneNumber(),
```

### Address Data
```php
'address' => fake()->address(),
'street' => fake()->streetAddress(),
'city' => fake()->city(),
'state' => fake()->state(),
'zip' => fake()->postcode(),
'country' => fake()->country(),
```

### Lorem Ipsum
```php
'keywords' => fake()->words(3, true),

'title' => fake()->sentence(),
'title' => fake()->sentence(3, false),

'description' => fake()->paragraph(),
'content' => fake()->paragraphs(3, true),
'content' => fake()->paragraphs(3),

'excerpt' => fake()->text(200),
'summary' => fake()->realText(150),
```

---

## Complete Task Factory Example
```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4, false),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pendiente', 'en progreso', 'completada']),
            'priority' => fake()->numberBetween(1, 5),
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+2 months'),
            'user_id' => User::factory(),
            'is_archived' => fake()->boolean(10),
            'completed_at' => null,
        ];
    }
}
```

---

## Factory States

States allow you to define variations of your factory.

### Basic States
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => 'pendiente',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completada',
            'completed_at' => now(),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en progreso',
            'started_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pendiente',
            'due_date' => fake()->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 5,
        ]);
    }

    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}
```

### Using States
```php

$task = Task::factory()->completed()->create();

$task = Task::factory()
    ->overdue()
    ->highPriority()
    ->create();

$task = Task::factory()
    ->inProgress()
    ->withoutDescription()
    ->create();

$tasks = Task::factory()
    ->completed()
    ->count(5)
    ->create();
```

---

## Relationships

### BelongsTo Relationships
```php
public function definition(): array
{
    return [
        'title' => fake()->sentence(),
        'user_id' => User::factory(),
    ];
}

$task = Task::factory()->create();

$user = User::factory()->create();
$task = Task::factory()->create([
    'user_id' => $user->id,
]);

$user = User::factory()->create();
$task = Task::factory()->for($user)->create();
```

### HasMany Relationships
```php
$user = User::factory()
    ->has(Task::factory()->count(3))
    ->create();

$user = User::factory()
    ->hasTasks(5)
    ->create();

$user = User::factory()
    ->has(
        Task::factory()
            ->completed()
            ->count(3)
    )
    ->create();
```

### Many-to-Many Relationships
```php
$task = Task::factory()
    ->hasAttached(
        Tag::factory()->count(3),
        ['created_at' => now()]
    )
    ->create();

$tags = Tag::factory()->count(3)->create();
$task = Task::factory()
    ->hasAttached($tags)
    ->create();
```

---

## Complex Factory Examples

### User Factory with Profile
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'avatar' => fake()->optional(0.7)->imageUrl(200, 200, 'people'),
            'bio' => fake()->optional(0.8)->paragraph(),
            'timezone' => fake()->timezone(),
            'locale' => fake()->randomElement(['en', 'es', 'fr']),
            'is_admin' => fake()->boolean(5),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
```

### Project Factory with Tasks
```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
        
        return [
            'name' => fake()->catchPhrase(),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(['planning', 'active', 'completed', 'on-hold']),
            'start_date' => $startDate,
            'end_date' => fake()->optional(0.6)->dateTimeBetween($startDate, '+1 year'),
            'budget' => fake()->randomFloat(2, 10000, 500000),
            'owner_id' => User::factory(),
            'is_public' => fake()->boolean(30),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function withTasks(int $count = 5): static
    {
        return $this->has(
            Task::factory()->count($count),
            'tasks'
        );
    }

    public function completed(): static
    {
        $startDate = fake()->dateTimeBetween('-1 year', '-2 months');
        
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '-1 month'),
        ]);
    }
}
```

---

## Sequences

Create variations across multiple records.

### Basic Sequence
```php
Task::factory()
    ->count(6)
    ->sequence(
        ['status' => 'pendiente'],
        ['status' => 'en progreso'],
        ['status' => 'completada'],
    )
    ->create();
```

### Sequence with Closure
```php
Task::factory()
    ->count(5)
    ->sequence(fn (Sequence $sequence) => [
        'priority' => $sequence->index + 1,
        'title' => 'Task ' . ($sequence->index + 1),
    ])
    ->create();
```

### Complex Sequence
```php
$startDate = now()->subDays(10);

Task::factory()
    ->count(10)
    ->sequence(fn (Sequence $sequence) => [
        'created_at' => $startDate->copy()->addDays($sequence->index),
        'priority' => ($sequence->index % 3) + 1,
        'status' => $sequence->index < 5 ? 'completada' : 'pendiente',
    ])
    ->create();
```

---

## Callbacks (After Creating/Making)

### afterMaking

Runs after factory builds model but before saving.
```php
public function definition(): array
{
    return [
        'title' => fake()->sentence(),
    ];
}

public function configure(): static
{
    return $this->afterMaking(function (Task $task) {
        $task->slug = Str::slug($task->title);
    });
}
```

### afterCreating

Runs after model is saved to database.
```php
public function configure(): static
{
    return $this->afterCreating(function (Task $task) {
        $task->comments()->create([
            'body' => 'Initial comment',
            'user_id' => $task->user_id,
        ]);
        
        $task->user->notify(new TaskCreated($task));
    });
}
```

---

## Best Practices

### ✅ Do

1. **Keep data realistic**
```php
   ✅ 'title' => fake()->sentence(4, false),
   ❌ 'title' => 'Task ' . $this->faker->randomNumber(),
```

2. **Use appropriate faker methods**
```php
   ✅ 'email' => fake()->safeEmail(),
   ❌ 'email' => fake()->word() . '@example.com',
```

3. **Match validation rules**
```php
   ✅ 'title' => fake()->sentence(10),
   ❌ 'title' => fake()->paragraphs(3, true),
```

4. **Use optional() for nullable fields**
```php
   ✅ 'description' => fake()->optional(0.7)->paragraph(),
   ❌ 'description' => fake()->paragraph(),
```

5. **Create related models properly**
```php
   ✅ 'user_id' => User::factory(),
   ❌ 'user_id' => 1,
```

### ❌ Don't

1. **Don't use hardcoded values**
```php
   ❌ 'status' => 'pendiente',
   ✅ 'status' => fake()->randomElement(['pendiente', 'en progreso']),
```

2. **Don't create unnecessary data**
```php
   ❌ 'metadata' => json_encode([...])
   ✅ 'metadata' => null,
```

3. **Don't ignore relationships**
```php
   ❌ 'user_id' => null,
   ✅ 'user_id' => User::factory(),
```

---

## Testing with Factories

### In Tests
```php
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Specific Title',
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Specific Title',
        ]);
    }

    public function test_completed_tasks_are_marked_correctly(): void
    {
        $completedTask = Task::factory()->completed()->create();
        $pendingTask = Task::factory()->create();

        $this->assertEquals('completada', $completedTask->status);
        $this->assertNotNull($completedTask->completed_at);
        $this->assertEquals('pendiente', $pendingTask->status);
    }
}
```

---

## Common Patterns

### Pattern 1: Progressive Dates
```php
public function definition(): array
{
    $createdAt = fake()->dateTimeBetween('-1 month', 'now');
    $startedAt = fake()->dateTimeBetween($createdAt, 'now');
    
    return [
        'created_at' => $createdAt,
        'started_at' => $startedAt,
        'completed_at' => fake()->optional(0.5)->dateTimeBetween($startedAt, 'now'),
    ];
}
```

### Pattern 2: Conditional Fields
```php
public function definition(): array
{
    $isCompleted = fake()->boolean(30);
    
    return [
        'status' => $isCompleted ? 'completada' : fake()->randomElement(['pendiente', 'en progreso']),
        'completed_at' => $isCompleted ? fake()->dateTimeBetween('-1 week', 'now') : null,
    ];
}
```

### Pattern 3: Calculated Fields
```php
public function definition(): array
{
    $title = fake()->sentence(4, false);
    
    return [
        'title' => $title,
        'slug' => Str::slug($title),
        'word_count' => str_word_count($title),
    ];
}
```

---

## Related Documentation

- [Test Examples](./test-examples.md)
- [Testing Standards](../standards/testing.md)
- [Phase 2: Implementation](../workflow/phase-2-implementation.md)
- [Laravel Factories Documentation](https://laravel.com/docs/12.x/eloquent-factories)