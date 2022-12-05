<?php

namespace Database\Factories;

use App\Enums\Statuses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->realText(10),
            'status' => $this->faker->randomElement(array_column(Statuses::cases(), 'value')),
        ];
    }
}
