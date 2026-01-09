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
        Schema::table('vehicles', function (Blueprint $table) {
             $table->dropColumn('vehicle_make');
             $table->dropColumn('model_name');
             $table->dropColumn('vehicle_color');
             $table->dropColumn('driver_name');
             $table->dropColumn('driver_contact');
             $table->dropColumn('vendor_name');
             $table->dropColumn('purpose_type');
             $table->dropColumn('photo_path');
             $table->dropColumn('rfid_tag');
             $table->dropColumn('qr_code');
             $table->dropColumn('gate_permission_type');
             $table->dropColumn('valid_from');
             $table->dropColumn('valid_to');
             $table->dropColumn('insurance_expiry');
             $table->dropColumn('registration_date');
             $table->string('emp_code')->nullable()->comment('Employee Code');
             $table->string('name')->nullable()->comment('Contect No');
             $table->string('email_id')->nullable()->comment('Email Id');
             $table->string('driving_license_no')->nullable()->comment('Driving License No.');
             $table->date('driving_license_validity')->nullable()->comment('Driving License Validity');
             $table->date('rc_validity')->nullable()->comment('RC Validity Date');
             $table->date('puc_validity')->nullable()->comment('PUC Validity Date');
             $table->date('insurance_validity')->nullable()->comment('Insurance Validity Date');
             $table->string('residence')->nullable()->comment('Residence');
            

             
             
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            //
        });
    }
};
