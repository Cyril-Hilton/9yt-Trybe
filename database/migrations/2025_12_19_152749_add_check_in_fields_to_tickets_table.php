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
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by_staff')->nullable()->constrained('organization_staff')->nullOnDelete();
            $table->foreignId('checked_in_by_company')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('check_in_method')->nullable(); // 'qr' or 'manual'
            $table->string('check_in_ip')->nullable();
            $table->text('check_in_notes')->nullable();

            $table->index('checked_in_at');
            $table->index(['checked_in_by_staff', 'checked_in_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by_staff']);
            $table->dropForeign(['checked_in_by_company']);
            $table->dropColumn([
                'checked_in_at',
                'checked_in_by_staff',
                'checked_in_by_company',
                'check_in_method',
                'check_in_ip',
                'check_in_notes'
            ]);
        });
    }
};
