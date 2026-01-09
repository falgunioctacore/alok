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
        Schema::create('empolyees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_name',120)->nullable();
            $table->string('emp_code',48)->nullable();
            $table->string('emp_email_id')->nullable();
            $table->string('site_area_id',48)->nullable();
            $table->string('plant_id',48)->nullable();
            $table->string('department_id',48)->nullable();
            $table->string('emp_mobile_no',48)->nullable();
            $table->softDeletes();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empolyees');
    }
};
