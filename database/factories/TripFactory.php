<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'customer_name' => $faker->name(),
            'pickup_address' => $faker->address(),
            'pickup_latitude' => $faker->latitude(),
            'pickup_longitude' => $faker->longitude(),
            'dropoff_address' => $faker->address(),
            'dropoff_latitude' => $faker->latitude(),
            'dropoff_longitude' => $faker->longitude(),
            'driver_id' => null,
            'status' => 'pending',
            'estimated_fare' => $faker->randomFloat(2, 5, 100),
            'version' => 1,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'driver_id' => null,
        ]);
    }

    public function assigned(Driver $driver): static
    {
        return $this->state(fn () => [
            'status' => 'assigned',
            'driver_id' => $driver->id,
        ]);
    }
}