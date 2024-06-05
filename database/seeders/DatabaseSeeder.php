<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\Regional;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $userId = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
        $user = User::create([
            'id' => $userId,
            'name' => 'Fatkul Umar',
            'email' => 'fatkulumar@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('fatkulumar'),
        ]);

        for ($i = 0; $i < 12; $i++) {
            Article::create([
                'user_id' => $userId,
                'title' => 'My First Article',
                'body' => 'ini adalah body article Lorem ipsum dolor sit amet consectetur, adipisicing elit. Error, deserunt aliquam nam ab sapiente nostrum quaerat rerum dignissimos quam voluptates sed dolorem recusandae. Dolor eos cumque voluptate, neque fugiat placeat?',
                'slug' => 'my-first-article',
                'label' => 'Label My First Article',
                'category' => 'Category My First Article',
            ]);

            $id_class_room = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
            ClassRoom::create([
                'id' => $id_class_room,
                'name' => 'Class Room ke '. $i,
            ]);

            $id_category = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
            Category::create([
                'id' => $id_category,
                'name' => 'Category ke '. $i,
            ]);

            $id_regional = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
            Regional::create([
                'id' => $id_regional,
                'name' => 'Regional ke '. $i,
            ]);
        }
    }
}
