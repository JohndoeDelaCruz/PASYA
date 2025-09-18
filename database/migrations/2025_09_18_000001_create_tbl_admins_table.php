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
        Schema::create('tblAdmins', function (Blueprint $table) {
            $table->id('adminID');
            $table->string('adminName');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('position')->nullable()->comment('Admin position/role');
            $table->string('department')->nullable()->comment('Department or division');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblAdmins');
    }
};