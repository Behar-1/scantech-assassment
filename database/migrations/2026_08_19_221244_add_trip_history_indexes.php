<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_status_histories', function (Blueprint $table) {
            $table->index(
                ['trip_id', 'id'],
                'trip_status_histories_trip_id_id_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('trip_status_histories', function (Blueprint $table) {
            $table->dropIndex(
                'trip_status_histories_trip_id_id_index'
            );
        });
    }
};