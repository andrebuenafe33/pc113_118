<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Get all filenames from the public/profile_images folder
        // $images = File::files(public_path('images'));
        // $randomImage = count($images) > 0 
        //     ? 'images/' . $images[array_rand($images)]->getFilename()
        //     : null;

        // Get all image filenames from storage/app/public/profile_images/
        $files = Storage::disk('public')->files('profile_images');

        $randomImage = count($files) > 0
            ? $files[array_rand($files)]
            : null;
            
        $adminRole = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $staffRole = Role::create(['name' => 'Staff']);

        User::factory()->create([
            'profile_image' => $randomImage, 
            'role_id' => $adminRole->id,
            'email' => 'admin@example.com',
            'name' => 'Admin User',
            'email_verified_at' => now(),
            'password' => Hash::make('admin231'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Employee::factory(20)->create(); 
    }
}
