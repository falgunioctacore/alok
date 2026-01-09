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
        Schema::create('vehicle_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id',36)->nullable();
            $table->string('emp_code',48)->nullable();
            $table->dateTime('attendence_date_time')->nullable();
            $table->dateTime('in_time')->nullable();
            $table->dateTime('out_time')->nullable();
            $table->string('in_geo_fencing_point_id',48)->nullable();
            $table->string('out_geo_fencing_point_id',48)->nullable();
            $table->string('in_latitude',120)->nullable();
            $table->string('in_longitude',120)->nullable();
            $table->string('out_latitude',120)->nullable();
            $table->string('out_longitude',120)->nullable();
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
        Schema::dropIfExists('vehicle_attendances');
    }
};
