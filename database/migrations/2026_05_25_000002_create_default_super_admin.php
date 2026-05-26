<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $email = 'superadmin@gymsaas.com';
        
        $exists = DB::table('users')->where('email', $email)->exists();
        
        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'Platform Super Admin',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'superadmin@gymsaas.com')->delete();
    }
};
