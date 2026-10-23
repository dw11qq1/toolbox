<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 后台管理员（幂等，可重复执行）
        User::updateOrCreate(
            ['email' => '3023207113@qq.com'],
            ['name' => 'admin', 'password' => Hash::make('Toolbox@2026admin')],
        );

        $this->call(ContentSeeder::class);
    }
}
