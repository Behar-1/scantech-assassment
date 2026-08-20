<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
         $faker = FakerFactory::create();
        return [
            'name' => $faker->name(),
            'status' => 'available',
            'last_latitude' => $faker->latitude(),
            'last_longitude' =>  $faker->longitude(),
            'last_location_at' => now(),
        ];
    }

    public function available(): static
    {
        return $this->state(fn () => [
            'status' => 'available',
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn () => [
            'status' => 'offline',
        ]);
    }

    public function assigned(): static
    {
        return $this->state(fn () => [
            'status' => 'assigned',
        ]);
    }

    public function onTrip(): static
    {
        return $this->state(fn () => [
            'status' => 'on_trip',
        ]);
    }
}