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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();

            // Create unsignedBigInteger columns first
            $table->unsignedBigInteger('translation_key_id');
            $table->unsignedBigInteger('locale_id');

            $table->text('content');
            $table->timestamps();

            // Add foreign key constraints separately
            $table->foreign('translation_key_id')->references('id')->on('translation_keys')->onDelete('cascade');
            $table->foreign('locale_id')->references('id')->on('locales')->onDelete('cascade');

            // Add a unique constraint to prevent duplicates
            $table->unique(['translation_key_id', 'locale_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
