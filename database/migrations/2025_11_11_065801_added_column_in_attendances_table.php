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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('in_geo_fencing_point_id',48)->nullable();
            $table->string('out_geo_fencing_point_id',48)->nullable();

            $table->string('in_latitude', 48)->nullable();
            $table->string('in_longitude', 48)->nullable();
            $table->string('out_latitude', 48)->nullable();
            $table->string('out_longitude', 48)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('in_access_point_id');
            $table->dropColumn('out_access_point_id');

            $table->dropColumn('in_latitude');
            $table->dropColumn('in_longitude');
            $table->dropColumn('out_latitude');
            $table->dropColumn('out_longitude');
      
            
        });
    }
};
