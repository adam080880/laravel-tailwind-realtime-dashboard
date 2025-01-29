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
        Schema::create('stream_data', function (Blueprint $table) {
            $table->id();
            $table->string('pointName');
            $table->double('pointValue');
            $table->string('pointQuality');
            $table->datetime('pointTimestamp');
            $table->integer('idPointAlias');
            $table->datetime('insertTimestamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stream_data');
    }
};
