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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('dob');
            $table->decimal('balance',8,2)->default(0);
            $table->decimal('commission',8,2)->default(0);
            $table->string('tx_pin')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('local_gov')->nullable();
            $table->string('address')->nullable();
            $table->string('bvn')->unique()->nullable();
            $table->string('nin')->unique()->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('isAggregator')->default(0);
            $table->string('role')->default('user');
            $table->string('gender')->nullable();
            $table->string('image')->nullable();
            $table->string('device_model');
            $table->string('device_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('bvn_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        
            // $table->string('nationality');
            // $table->string('confirm_password');
            // $table->string('country_code');
          
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
