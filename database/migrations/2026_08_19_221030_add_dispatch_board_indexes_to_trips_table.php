<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->index(['status', 'id'], 'trips_status_id_index');
            $table->index(['driver_id', 'status'], 'trips_driver_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex('trips_status_id_index');
            $table->dropIndex('trips_driver_status_index');
        });
    }
};