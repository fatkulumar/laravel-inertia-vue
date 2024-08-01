<?php

namespace Database\Seeders;

use App\Models\AppointmentFile;
use App\Models\Article;
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\Documentation;
use App\Models\GuideCadre;
use App\Models\Letter;
use App\Models\Profile;
use App\Models\RegencyRegional;
use App\Models\Regional;
use App\Models\Schedule;
use App\Models\Speaker;
use App\Models\Submission;
use App\Models\TypeActivity;
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

                $id_regional1 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Regional::create([
                    'id' => $id_regional1,
                    'name' => 'Surabaya',
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

                $id_regional2 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Regional::create([
                    'id' => $id_regional2,
                    'name' => 'Ngawi',
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

                Profile::create([
                    'address' => 'Jl. Cempaka No. 2',
                    'regional_id' => $id_regional2,
                    'profileable_id' => $userId2,
                    'profileable_type' => 'App\Models\User',
                    'hp' => '6281234567890',
                    'gender' => 'Laki-laki',
                ]);

                //

                $userIdPeserta2 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                $userPeserta2 = User::create([
                    'id' => $userIdPeserta2,
                    'name' => 'Peserta dua',
                    'email' => 'peserta2@gmail.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('fatkulumar'),
                    'image' => 'image',
                ]);

                Role::updateOrCreate(['name' => 'peserta']);
                $userPeserta2->assignRole('peserta');

                Profile::create([
                    'address' => 'Jl. Cempaka No. 2',
                    'regional_id' => $id_regional2,
                    'profileable_id' => $userIdPeserta2,
                    'profileable_type' => 'App\Models\User',
                    'hp' => '6281234567890',
                    'gender' => 'perempuan',
                ]);
            }

            if($i == 2) {
                $Category3 = Category::create([
                    'name' => 'Advanced ' . $i ,
                ]);

                $classRoom3 = ClassRoom::create([
                    'name' => 'Kelas ' . $i,
                ]);

                $id_regional3 = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Regional::create([
                    'id' => $id_regional3,
                    'name' => 'Malang',
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

                Profile::create([
                    'address' => 'Jl. Cempaka No. 3',
                    'regional_id' => $id_regional3,
                    'profileable_id' => $userId3,
                    'profileable_type' => 'App\Models\User',
                    'hp' => '6281234567890',
                ]);

                $type_activity_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                TypeActivity::create([
                    'id' => $type_activity_id,
                    'name' => 'Kopdar Meida',
                ]);

                $speaker_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Speaker::create([
                    'id' => $speaker_id,
                    'image' => 'foto spealer',
                    'name' => 'Speaker Umar',
                    'province_code' => 11,
                    'city_code' => 1101,
                    'class_room_id' => $classRoom3->id,
                    'category_id' => $Category1->id,
                ]);

                $regency_regional_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                RegencyRegional::create([
                    'id' => $regency_regional_id,
                    'regional_id' => $id_regional3,
                    'regency' => 'Sukun',
                ]);

                $scheduleId = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Schedule::create([
                    'id' => $scheduleId,
                    'regional_id' => $id_regional3,
                    'committee_id' => $userId3,
                    'category_id' => $Category3->id,
                    'class_room_id' => $classRoom3->id,
                    'chief_id' => $userId3, //ketua pelaksana
                    'type_activity_id' => $type_activity_id,
                    'regency_regional_id' => $regency_regional_id,
                    'periode' => 14,
                    'poster' => 'Link Poster', //konsep kegiatan
                    'concept' => 'konsep', //konsep kegiatan
                    'committee_layout' => 'ketua pelaksana', //susunan panitia
                    'target_participant' => 'target peserta', //target peserta
                    'speaker_id' => $speaker_id, //pemateri //opsioanl
                    'total_activity' => 14, // total kegiatan yang sudah dikerjakan
                    'price' => 65000, // harga
                    'facility' => 'apapun fasilitas ada', // fasiliitas
                    'total_rooms_stay' => 2, // jumlah ruang menginap
                    'benefit' => 'benefitnya barokah', // jumlah ruang menginap
                    'location' => 'SMK Hati Patah 21',
                    'google_maps' => 'https://googlemaps.com',
                    'address' => 'Jl. Yang di ridhoi No. 3',
                    'status' => 'approval',
                    'start_date_class' => now(),
                    'end_date_class' => now(),
                    'graduation_date' => now(),
                    // 'proposal' => 'Link File',
                    'date_overview' => now(),
                    'date_received' => now(),
                ]);

                Submission::create([
                    'id' => substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36),
                    'schedule_id' => $scheduleId,
                    'participant_id' => $userId2,
                    'proof' => 'Link Proof',
                    'status' => 'approved',
                ]);

                Submission::create([
                    'id' => substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36),
                    'schedule_id' => $scheduleId,
                    'participant_id' => $userIdPeserta2,
                    'proof' => 'Link Proof',
                    'status' => 'graduated',
                ]);

                $letter_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Letter::create([
                    'id' => $letter_id,
                    'schedule_id' => $scheduleId,
                    'file' => 'ini surat tugas',
                    'name' => 'name file',
                ]);

                $documentation_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                Documentation::create([
                    'id' => $documentation_id,
                    'schedule_id' => $scheduleId,
                    'title' => 'documentation 1',
                    'description' => 'ini adalah deslripsi 1',
                    'image' => 'image.jpg',
                ]);

                $appointmentFile_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                AppointmentFile::create([
                    'id' => $appointmentFile_id,
                    'schedule_id' => $scheduleId,
                    'name' => 'name',
                    'file' => 'file',
                ]);

                $guide_cadre_id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
                GuideCadre::create([
                    'id' => $guide_cadre_id,
                    'type_activity_id' => $type_activity_id,
                    'name' => 'name',
                    'link' => 'link',
                    'information' => 'Persiapan Pelaksanaan Kegiatan',
                ]);
            }
        }
    }
}
