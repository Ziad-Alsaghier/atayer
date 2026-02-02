<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
{
    Schema::table('modules', function (Blueprint $table) {
        if (!Schema::hasColumn('modules', 'status')) {
            $table->boolean('status')->default(true);
        }
    });
}

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
