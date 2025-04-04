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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('application_name');
            $table->string('footer_text');
            $table->string('colored_logo')->nullable();
            $table->string('light_logo')->nullable();
            // $table->string('language_code')->nullable();
            // $table->string('language_name')->nullable();
            $table->string('active_sms_api')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
