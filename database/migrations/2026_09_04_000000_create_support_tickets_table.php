<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_code')->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('type', ['complaint', 'inquiry'])->default('inquiry');
                $table->string('category')->nullable();
                $table->string('related_id')->nullable();
                $table->string('subject');
                $table->text('message');
                $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected', 'closed'])->default('pending');
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('admin_response')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();
            });
        } else {
            DB::statement("ALTER TABLE support_tickets MODIFY status ENUM('pending', 'in_progress', 'resolved', 'rejected', 'closed') NOT NULL DEFAULT 'pending';");
            DB::statement("ALTER TABLE support_tickets MODIFY type ENUM('complaint', 'inquiry') NOT NULL DEFAULT 'inquiry';");
            Schema::table('support_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('support_tickets', 'details')) {
                    $table->text('details')->nullable()->change();
                }
                if (!Schema::hasColumn('support_tickets', 'ticket_code')) {
                    $table->string('ticket_code')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('support_tickets', 'category')) {
                    $table->string('category')->nullable()->after('type');
                }
                if (!Schema::hasColumn('support_tickets', 'related_id')) {
                    $table->string('related_id')->nullable()->after('category');
                }
                if (!Schema::hasColumn('support_tickets', 'priority')) {
                    $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
                }
                if (!Schema::hasColumn('support_tickets', 'admin_id')) {
                    $table->foreignId('admin_id')->nullable()->after('priority')->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('support_tickets', 'admin_response')) {
                    $table->text('admin_response')->nullable()->after('admin_id');
                }
                if (!Schema::hasColumn('support_tickets', 'responded_at')) {
                    $table->timestamp('responded_at')->nullable()->after('admin_response');
                }
            });

            // Populate ticket_code for any existing rows that lack one
            $existingTickets = DB::table('support_tickets')->whereNull('ticket_code')->get();
            foreach ($existingTickets as $t) {
                $prefix = ($t->type === 'complaint') ? 'CP-' : 'INQ-';
                DB::table('support_tickets')
                    ->where('id', $t->id)
                    ->update(['ticket_code' => $prefix . (1000 + $t->id)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback
    }
};
