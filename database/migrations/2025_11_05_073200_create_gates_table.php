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
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->string('gate_code',120)->nullable();
            $table->string('gate_name',120)->nullable();
            $table->string('location',120)->nullable();
            $table->string('gate_type',120)->nullable();
            $table->string('description',120)->nullable();
            $table->boolean('status')->default(True);
            $table->string('created_by',24)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gates');
    }
};
