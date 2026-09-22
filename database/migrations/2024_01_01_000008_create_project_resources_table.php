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
        Schema::create('project_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('resource_name');
            $table->string('resource_type'); // equipment, material, human_resources, etc.
            $table->decimal('quantity', 8, 2);
            $table->string('unit'); // pieces, hours, kg, etc.
            $table->decimal('cost_per_unit', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->enum('status', ['available', 'allocated', 'used', 'disposed'])->default('available');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_resources');
    }
};
