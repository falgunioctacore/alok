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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no', 20)->unique()->comment('Vehicle registration number');
            $table->string('vehicle_type', 50)->nullable()->comment('Car, Truck, Bus, Bike, etc.');
            $table->string('vehicle_make', 100)->nullable()->comment('Brand or manufacturer name');
            $table->string('model_name', 100)->nullable()->comment('Vehicle model name');
            
            $table->string('vehicle_color', 50)->nullable()->comment('Vehicle color');
            $table->string('driver_name', 100)->nullable()->comment('Driver full name');
            $table->string('driver_contact', 15)->nullable()->comment('Driver mobile number');
            $table->string('driver_license_no', 50)->nullable()->comment('Driver license number');

            $table->unsignedBigInteger('employee_id')->nullable()->comment('Linked employee ID from permitted_employees_master');
            $table->string('vendor_name', 150)->nullable()->comment('Vendor or contractor name');
            
            $table->enum('purpose_type', ['Employee', 'Visitor', 'Vendor', 'Company'])
                  ->default('Employee')->comment('Vehicle belongs to whom');
            
            $table->text('photo_path')->nullable()->comment('Vehicle photo path or URL');
            $table->string('rfid_tag', 100)->nullable()->comment('RFID tag number');
            $table->text('qr_code')->nullable()->comment('QR code data for scanning');

            $table->enum('gate_permission_type', ['All', 'Specific'])->default('All')
                  ->comment('Allowed for all gates or specific ones');
            $table->text('allowed_gate_ids')->nullable()->comment('Comma separated gate IDs if specific');

            $table->date('valid_from')->nullable()->comment('Permission valid from date');
            $table->date('valid_to')->nullable()->comment('Permission valid to date');
            
            $table->enum('status', ['Active', 'Inactive'])->default('Active')
                  ->comment('Active = allowed, Inactive = blocked');

            $table->date('registration_date')->nullable()->comment('Vehicle registration date');
            $table->date('insurance_expiry')->nullable()->comment('Insurance expiry date');

            $table->string('created_by',120)->nullable()->comment('Created by user ID');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
