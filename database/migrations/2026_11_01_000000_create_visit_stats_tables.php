<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->date('viewed_on');
            $table->string('path', 191);
            $table->unsignedBigInteger('hits')->default(0);
            $table->unique(['viewed_on', 'path']);
        });

        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->date('viewed_on');
            $table->string('visitor_hash', 32);
            $table->unique(['viewed_on', 'visitor_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('page_views');
    }
};
