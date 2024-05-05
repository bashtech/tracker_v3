<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `tickets` CHANGE `state` `state` enum('new','assigned','resolved','rejected','on_hold','in_progress') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new';");
        });
    }

};
