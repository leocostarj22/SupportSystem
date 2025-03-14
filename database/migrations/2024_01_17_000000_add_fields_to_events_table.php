<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'title')) {
                $table->string('title')->after('id');
            }
            if (!Schema::hasColumn('events', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('events', 'start')) {
                $table->dateTime('start')->after('description');
            }
            if (!Schema::hasColumn('events', 'end')) {
                $table->dateTime('end')->after('start');
            }
            if (!Schema::hasColumn('events', 'color')) {
                $table->string('color')->nullable()->after('end');
            }
            if (!Schema::hasColumn('events', 'user_id')) {
                $table->foreignId('user_id')->constrained()->cascadeOnDelete()->after('color');
            }
            if (!Schema::hasColumn('events', 'all_day')) {
                $table->boolean('all_day')->default(false)->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'start',
                'end',
                'color',
                'user_id',
                'all_day'
            ]);
        });
    }
};