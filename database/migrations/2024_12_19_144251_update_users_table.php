<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable(); 
            $table->string('permissions')->nullable(); 
            $table->string('trial_ends_at')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->string('other_attributes')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverse the changes made in up()
            $table->dropColumn('last_name');
            $table->dropColumn('permissions');
            $table->dropColumn('trial_ends_at');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('other_attributes');
        });
    }
};
