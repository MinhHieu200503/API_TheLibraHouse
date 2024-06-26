<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        $user = new User();
        $user->username = 'Admin';
        $user->email = 'ldmhieudev@gmail.com';
        $user->password = bcrypt('123');
        $user->phone = '0123456789';
        $user->role = "admin";
        $user->url_avatar = env('SERVER_DOMAIN','http://localhost:8000').'/storage/default.png';
        $user->save();

        $admin = new Admin();
        $admin->user_id = $user->id;
        $admin->save();
        
        //category
        $category  = new Category();
        $category->name = "danh mục mẫu";
        $category->description = "Mẩu khởi tạo dbs";
        $category->save();

        $category  = new Category();
        $category->name = "danh mục mẫu - 2";
        $category->description = "Mẩu khởi tạo dbs";
        $category->save();

    }
}