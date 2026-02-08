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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reactable_id');
            $table->string('reactable_type');
            $table->string('reaction_type'); // 'thumbs_up', 'heart', 'celebrate', 'rocket'
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Composite unique index to prevent duplicate reactions from same IP
            $table->unique(['reactable_id', 'reactable_type', 'reaction_type', 'ip_address'],
                'reactions_unique_reactable_ip_type');

            // Index for polymorphic queries
            $table->index(['reactable_id', 'reactable_type'], 'reactions_reactable_index');

            // Index for reaction type aggregation
            $table->index('reaction_type', 'reactions_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
