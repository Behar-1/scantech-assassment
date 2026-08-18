<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Diana Dispatcher', 'email' => 'dispatcher@scantech.local', 'role' => 'dispatcher', 'can_dispatch' => true],
            ['name' => 'Sam Supervisor', 'email' => 'supervisor@scantech.local', 'role' => 'supervisor', 'can_dispatch' => true],
            ['name' => 'Ada Administrator', 'email' => 'admin@scantech.local', 'role' => 'administrator', 'can_dispatch' => false],
        ];

        foreach ($users as $user) {
            User::query()->create($user + ['password' => Hash::make('password')]);
        }

        $driverNames = [
            'Arben Krasniqi', 'Besnik Gashi', 'Driton Berisha', 'Erion Hoxha', 'Fisnik Shala',
            'Gent Leka', 'Ilir Dema', 'Jeton Kelmendi', 'Kushtrim Rexha', 'Luan Bytyqi',
            'Mentor Haliti', 'Naim Morina', 'Orges Kola', 'Petrit Rama', 'Qendrim Mustafa',
            'Rinor Veseli', 'Shkelzen Kastrati', 'Trim Ahmeti', 'Valon Selimi', 'Yll Dervishi',
        ];

        for ($i = 1; $i <= 60; $i++) {
            $baseName = $driverNames[($i - 1) % count($driverNames)];
            Driver::query()->create([
                'name' => $baseName . ' ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => $i <= 23 ? 'assigned' : ($i <= 50 ? 'available' : 'offline'),
                'last_latitude' => 42.6629 + (($i % 9) * 0.0011),
                'last_longitude' => 21.1655 + (($i % 11) * 0.0010),
                'last_location_at' => now()->subMinutes($i % 25),
            ]);
        }

        $pickupAddresses = [
            'Mother Teresa Square, Prishtina', 'Rr. B, Prishtina', 'Albi Mall, Veternik',
            'Prishtina Bus Station', 'University Clinical Center, Prishtina', 'Dardania, Prishtina',
            'Ulpiana, Prishtina', 'Bregu i Diellit, Prishtina', 'Arberia, Prishtina',
            'Prishtina International Airport',
        ];

        $dropoffAddresses = [
            'Germia Park, Prishtina', 'Grand Hotel, Prishtina', 'Rr. C, Prishtina',
            'Newborn Monument, Prishtina', 'Central Railway Station, Prishtina',
            'Lipjan Center', 'Fushe Kosove Center', 'Mati 1, Prishtina', 'Dragodan, Prishtina',
            'Emerald Hotel, Prishtina',
        ];

        $statuses = array_merge(
            array_fill(0, 700, 'pending'),
            array_fill(0, 10, 'assigned'),
            array_fill(0, 8, 'driver_arriving'),
            array_fill(0, 5, 'in_progress'),
            array_fill(0, 30, 'completed'),
            array_fill(0, 17, 'cancelled'),
        );

        $activeDriver = 1;
        $actorId = User::query()->where('role', 'dispatcher')->value('id');

        foreach ($statuses as $index => $status) {
            $driverId = null;

            if (in_array($status, ['assigned', 'driver_arriving', 'in_progress'], true)) {
                $driverId = $activeDriver++;
            } elseif (in_array($status, ['completed', 'cancelled'], true) && $index % 3 !== 0) {
                $driverId = 24 + ($index % 27);
            }

            $trip = Trip::query()->create([
                'customer_name' => 'Customer ' . str_pad((string)($index + 1), 4, '0', STR_PAD_LEFT),
                'pickup_address' => $pickupAddresses[$index % count($pickupAddresses)],
                'pickup_latitude' => 42.6500 + (($index % 40) * 0.0008),
                'pickup_longitude' => 21.1500 + (($index % 35) * 0.0009),
                'dropoff_address' => $dropoffAddresses[($index + 3) % count($dropoffAddresses)],
                'dropoff_latitude' => 42.6400 + (($index % 31) * 0.0010),
                'dropoff_longitude' => 21.1600 + (($index % 29) * 0.0007),
                'driver_id' => $driverId,
                'status' => $status,
                'estimated_fare' => 3.50 + (($index % 45) * 0.35),
                'version' => 1 + ($index % 4),
                'created_at' => now()->subMinutes($index),
                'updated_at' => now()->subMinutes($index % 50),
            ]);

            if ($status !== 'pending' && $index % 4 !== 0) {
                TripStatusHistory::query()->create([
                    'trip_id' => $trip->id,
                    'previous_status' => $status === 'assigned' ? 'pending' : null,
                    'new_status' => $status,
                    'changed_by' => $actorId,
                    'metadata' => ['seeded' => true],
                    'created_at' => $trip->updated_at,
                ]);
            }
        }

        Driver::query()->whereBetween('id', [19, 23])->update(['status' => 'on_trip']);
    }
}
