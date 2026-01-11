<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('state')->nullable()->after('current_location');
            $table->string('city')->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn(['state', 'city']);
        });
    }
};
