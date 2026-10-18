<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sessions')) {
            if (! Schema::hasColumns('sessions', [
                'id',
                'user_id',
                'ip_address',
                'user_agent',
                'payload',
                'last_activity',
            ])) {
                throw new RuntimeException('The existing sessions table does not have the expected Laravel session columns.');
            }

            return;
        }

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        // The table may predate this migration, so rolling it back must not delete session data.
    }
};
