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
        Schema::create('reasons', function (Blueprint $table) {
            $table->id();
            $table->string('reason_name', 150)->nullable()->comment('Reason for going out');
            $table->text('description')->nullable()->comment('Detailed description if needed');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->comment('Active = visible, Inactive = hidden');
            $table->string('created_by',120)->nullable()->comment('User who created this reason');
            $table->softDeletes();
            $table->timestamps();
            

            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reasons');
    }
};
