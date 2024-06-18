<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\Profile;
use App\Models\Regional;
use App\Models\Submission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        for ($i = 0; $i < 12; $i++) {

            if($i == 0) {
                $Category1 = Category::create([
                    'name' => 'Basic ' . $i ,
                ]);

                $kclassRoom1 = ClassRoom::create([
                    'name' => 'Kelas ' . $i,
                ]);
                $userId1 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                $user1 = User::create([
                    'id' => $userId1,
                    'name' => 'Fatkul Umar',
                    'email' => 'fatkulumar@gmail.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('fatkulumar'),
                    'image' => 'image',
                ]);

                Role::create(['name' => 'admin']);
                $user1->assignRole('admin');

                $id_regional1 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Regional::create([
                    'id' => $id_regional1,
                    'name' => 'Surabaya',
                ]);

                Profile::create([
                    'address' => 'Jl. Cempaka No. 1',
                    'regional_id' => $id_regional1,
                    'profileable_id' => $userId1,
                    'profileable_type' => 'App\Models\User',
                    'hp' => '6281234567890',
                ]);
            }

            if($i == 1) {
                $Category2 = Category::create([
                    'name' => 'Intermediate ' . $i ,
                ]);

                $kclassRoom2 = ClassRoom::create([
                    'name' => 'Kelas ' . $i,
                ]);
                $userId2 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                $user2 = User::create([
                    'id' => $userId2,
                    'name' => 'Peserta Umar',
                    'email' => 'peserta@gmail.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('fatkulumar'),
                    'image' => 'image',
                ]);

                Role::create(['name' => 'peserta']);
                $user2->assignRole('peserta');

                $id_regional2 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Regional::create([
                    'id' => $id_regional2,
                    'name' => 'Ngawi',
                ]);

                Profile::create([
                    'address' => 'Jl. Cempaka No. 2',
                    'regional_id' => $id_regional2,
                    'profileable_id' => $userId2,
                    'profileable_type' => 'App\Models\User',
                    'hp' => '6281234567890',
                ]);
            }

            if($i == 2) {
                $Category3 = Category::create([
                    'name' => 'Advanced ' . $i ,
                ]);

                $kclassRoom3 = ClassRoom::create([
                    'name' => 'Kelas ' . $i,
                ]);
                $userId3 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                $user3 = User::create([
                    'id' => $userId3,
                    'name' => 'Panitia Umar',
                    'email' => 'panitia@gmail.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('fatkulumar'),
                    'image' => 'image',
                ]);

                Role::create(['name' => 'panitia']);
                $user3->assignRole('panitia');

                $id_regional3 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Regional::create([
                    'id' => $id_regional3,
                    'name' => 'Malang',
                ]);

                Profile::create([
                    'address' => 'Jl. Cempaka No. 3',
                    'regional_id' => $id_regional3,
                    'profileable_id' => $userId3,
                    'profileable_type' => 'App\Models\User',
                    'hp' => '6281234567890',
                ]);

                $submissionId = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Submission::create([
                    'id' => $submissionId,
                    'participant_id' => $userId2,
                    'committee_id' => $userId3,
                    'class_room_id' => $kclassRoom3->id,
                    'category_id' => $Category3->id,
                    'status' => 'pending',
                    'file' => 'Link File',
                    'start_date_class' => now(),
                    'end_date_class' => now(),
                    'periode' => 14,
                    'location' => 'SMK Hati Patah 21',
                    'google_maps' => 'https://googlemaps.com',
                    'address' => 'Jl. Yang di ridhoi No. 3',
                ]);
            }
        }
    }
}
