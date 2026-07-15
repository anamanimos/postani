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
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('gallery_label', function (Blueprint $table) {
            $table->foreignId('gallery_id')->constrained()->onDelete('cascade');
            $table->foreignId('label_id')->constrained()->onDelete('cascade');
            $table->primary(['gallery_id', 'label_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_label');
        Schema::dropIfExists('labels');
    }
};
