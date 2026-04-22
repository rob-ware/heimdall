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
        Schema::create('server_watches', function (Blueprint $table) {
            $table->id();
            $table->float('cpu_percentage');
            $table->float('ram_percentage');
            $table->integer('https_connections');
            $table->integer('ssh_connections');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_watches');
    }
};
