<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReservoirSafetyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'created_at' => fake()->dateTimeInInterval('-5 years'),
            'date_finished' => fake()->dateTimeThisYear(),
            'reservoir_id' => fake()->numberBetween(1, 30),
            'user_id' => fake()->numberBetween(1, 10),
            'main_dam_status' => fake()->boolean(),
            'main_dam_description' => fake()->sentence(),
            'spillway_status' => fake()->boolean(),
            'spillway_description' => fake()->sentence(),
            'mornitor_system_status' => fake()->boolean(),
            'mornitor_system_description' => fake()->sentence(),
        ];
    }
}
