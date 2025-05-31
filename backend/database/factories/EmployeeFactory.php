<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role_id' => 3 
            ]),
            'position' => $this->faker->jobTitle,
            'department' => $this->faker->randomElement(['HR', 'IT']),
            'hire_date' => $this->faker->date,
            'salary' => $this->faker->numberBetween(30000, 80000),
        ];
    }
}
