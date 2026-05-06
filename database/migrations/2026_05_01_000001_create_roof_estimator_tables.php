<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roof_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('base_price_per_square', 10, 2);
            $table->decimal('margin_percent', 5, 2);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('roof_pitches', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('multiplier', 8, 4)->default(1);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('roof_complexities', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('multiplier', 8, 4)->default(1);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('estimate_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('unit')->default('each');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('project_address')->nullable();
            $table->foreignId('roof_type_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('roof_pitch_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('roof_complexity_id')->constrained()->cascadeOnUpdate();
            $table->decimal('roof_area_squares', 10, 2);
            $table->decimal('waste_percent', 5, 2)->default(10);
            $table->unsignedInteger('tear_off_layers')->default(0);
            $table->unsignedInteger('stories')->default(1);
            $table->json('addon_quantities')->nullable();
            $table->json('calculation_snapshot');
            $table->decimal('material_cost', 12, 2);
            $table->decimal('addon_cost', 12, 2);
            $table->decimal('subtotal_cost', 12, 2);
            $table->decimal('margin_percent', 5, 2);
            $table->decimal('margin_amount', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('estimate_addons');
        Schema::dropIfExists('roof_complexities');
        Schema::dropIfExists('roof_pitches');
        Schema::dropIfExists('roof_types');
    }
};
