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
        Schema::create('get_in_out_logs', function (Blueprint $table) {
            $table->id();

            $table->string('gate_id')->nullable()->comment('Gate through which movement occurred');
            $table->string('employee_id')->nullable()->comment('Linked employee ID');
            $table->string('vehicle_id')->nullable()->comment('Linked vehicle ID');
            $table->enum('entry_type', ['IN', 'OUT'])->comment('Type of movement: IN or OUT');
            $table->dateTime('entry_time')->comment('Date and time of entry/exit');

            $table->string('user_id')->nullable()->comment('Security user who recorded this');
            $table->string('out_reason_id')->nullable()->comment('Reason for OUT movement');
            $table->text('other_reason')->nullable()->comment('If reason type is Other');
            $table->text('remarks')->nullable()->comment('Additional notes');
            $table->text('photo_path')->nullable()->comment('Photo captured at gate');
            $table->string('device_id', 50)->nullable()->comment('Device identifier (Zebra/Urovo)');
            $table->boolean('sync_status')->default(false)->comment('0 = Pending, 1 = Synced');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get_in_out_logs');
    }
};
