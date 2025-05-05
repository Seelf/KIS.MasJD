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
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_id');
            $table->string('key');
            $table->string('value')->nullable(); // będzie rzutowane na string
            $table->timestamp('info_timestamp')->nullable(); // z pola info.timestamp
            $table->timestamp('message_timestamp')->nullable(); // z głównego timestamp
            $table->string('message_type')->nullable(); // np. datapointValuesReceived
            $table->string('json_type')->nullable(); // np. centersightEvent
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
