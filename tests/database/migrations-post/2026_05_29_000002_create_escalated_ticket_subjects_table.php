<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mirrors escalated-laravel#122 until that migration ships in the package.
 */
return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('escalated.table_prefix', 'escalated_');

        if (Schema::hasTable($prefix.'ticket_subjects')) {
            return;
        }

        Schema::create($prefix.'ticket_subjects', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('role')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->foreign('ticket_id')
                ->references('id')
                ->on($prefix.'tickets')
                ->cascadeOnDelete();

            $table->unique(['ticket_id', 'subject_type', 'subject_id'], 'escalated_ticket_subject_unique');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        $prefix = config('escalated.table_prefix', 'escalated_');

        Schema::dropIfExists($prefix.'ticket_subjects');
    }
};
