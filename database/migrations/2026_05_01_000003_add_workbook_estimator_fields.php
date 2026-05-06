<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_rate_lines', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('category');
            $table->string('line_item');
            $table->string('unit', 50);
            $table->decimal('material_rate', 10, 2)->default(0);
            $table->decimal('labor_rate', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('is_admin');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->string('job_name')->nullable()->after('id');
            $table->json('input_snapshot')->nullable()->after('addon_quantities');
            $table->json('line_items')->nullable()->after('calculation_snapshot');
            $table->json('category_totals')->nullable()->after('line_items');
            $table->json('review_flags')->nullable()->after('category_totals');
            $table->decimal('direct_job_cost', 12, 2)->default(0)->after('review_flags');
            $table->decimal('recommended_sell', 12, 2)->default(0)->after('direct_job_cost');
            $table->decimal('gross_profit', 12, 2)->default(0)->after('recommended_sell');
        });
    }

    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn([
                'job_name',
                'input_snapshot',
                'line_items',
                'category_totals',
                'review_flags',
                'direct_job_cost',
                'recommended_sell',
                'gross_profit',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::dropIfExists('estimate_rate_lines');
    }
};
