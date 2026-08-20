<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'dispatcher',
            'can_dispatch' => true,
        ];
    }

    public function dispatcher(): static
    {
        return $this->state(fn () => [
            'role' => 'dispatcher',
            'can_dispatch' => true,
        ]);
    }

    public function supervisor(): static
    {
        return $this->state(fn () => [
            'role' => 'supervisor',
            'can_dispatch' => true,
        ]);
    }

    public function administrator(): static
    {
        return $this->state(fn () => [
            'role' => 'administrator',
            'can_dispatch' => false,
        ]);
    }
}