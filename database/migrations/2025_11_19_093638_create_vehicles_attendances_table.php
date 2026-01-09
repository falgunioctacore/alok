<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id',24)->nullable();
            $table->string('emp_code',48)->nullable();
            $table->string('type',120)->nullable();
            $table->dateTime('attendance_date')->nullable();
            $table->string('geo_fencing_point_id',48)->nullable();
            $table->string('latitude',120)->nullable();
            $table->string('longitude',120)->nullable();
            $table->string('reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles_attendances');
    }
};
