<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'slug' => 'first_course',
                'name_en' => 'First Step',
                'name_id' => 'Langkah Pertama',
                'description_en' => 'Completed your first course',
                'description_id' => 'Menyelesaikan course pertama Anda',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'bg-blue-100 text-blue-600',
                'requirement_type' => 1,
                'requirement_value' => 1,
            ],
            [
                'slug' => 'five_courses',
                'name_en' => 'Course Master',
                'name_id' => 'Master Course',
                'description_en' => 'Completed 5 courses',
                'description_id' => 'Menyelesaikan 5 course',
                'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
                'color' => 'bg-purple-100 text-purple-600',
                'requirement_type' => 1,
                'requirement_value' => 5,
            ],
            [
                'slug' => 'ten_courses',
                'name_en' => 'Learning Expert',
                'name_id' => 'Expert Belajar',
                'description_en' => 'Completed 10 courses',
                'description_id' => 'Menyelesaikan 10 course',
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                'color' => 'bg-orange-100 text-orange-600',
                'requirement_type' => 1,
                'requirement_value' => 10,
            ],
            [
                'slug' => 'fifty_hours',
                'name_en' => 'Dedication',
                'name_id' => 'Dedikasi',
                'description_en' => 'Completed 50 hours of learning',
                'description_id' => 'Menyelesaikan 50 jam belajar',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'bg-green-100 text-green-600',
                'requirement_type' => 2,
                'requirement_value' => 50,
            ],
            [
                'slug' => 'hundred_hours',
                'name_en' => 'Commitment',
                'name_id' => 'Komitmen',
                'description_en' => 'Completed 100 hours of learning',
                'description_id' => 'Menyelesaikan 100 jam belajar',
                'icon' => 'M9 12l2 2 4-4M7 20H5a2 2 0 01-2-2V9.828a2 2 0 01.586-1.414l5-5H15a2 2 0 012 2v10a2 2 0 01-2 2h-5.414a2 2 0 01-1.414-.586l-2-2H7v4z',
                'color' => 'bg-red-100 text-red-600',
                'requirement_type' => 2,
                'requirement_value' => 100,
            ],
            [
                'slug' => 'perfect_score',
                'name_en' => 'Perfect Score',
                'name_id' => 'Nilai Sempurna',
                'description_en' => 'Achieved 100% on a quiz',
                'description_id' => 'Mendapatkan 100% di sebuah kuis',
                'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-5a4 4 0 100 8 4 4 0 000-8z',
                'color' => 'bg-yellow-100 text-yellow-600',
                'requirement_type' => 3,
                'requirement_value' => 100,
            ],
            [
                'slug' => 'streak_7_days',
                'name_en' => 'Consistency',
                'name_id' => 'Konsistensi',
                'description_en' => 'Learned for 7 days in a row',
                'description_id' => 'Belajar selama 7 hari berturut-turut',
                'icon' => 'M17.657 18.657L13.414 22.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z',
                'color' => 'bg-cyan-100 text-cyan-600',
                'requirement_type' => 4,
                'requirement_value' => 7,
            ],
            [
                'slug' => 'streak_30_days',
                'name_en' => 'Persistence',
                'name_id' => 'Ketekunan',
                'description_en' => 'Learned for 30 days in a row',
                'description_id' => 'Belajar selama 30 hari berturut-turut',
                'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z',
                'color' => 'bg-indigo-100 text-indigo-600',
                'requirement_type' => 4,
                'requirement_value' => 30,
            ],
            [
                'slug' => 'top_performer',
                'name_en' => 'Top Performer',
                'name_id' => 'Performa Terbaik',
                'description_en' => 'In top 10 students of a course',
                'description_id' => 'Masuk 10 besar siswa di sebuah course',
                'icon' => 'M5 13l4 4L19 7',
                'color' => 'bg-pink-100 text-pink-600',
                'requirement_type' => 5,
                'requirement_value' => 10,
            ],
            [
                'slug' => 'fast_learner',
                'name_en' => 'Fast Learner',
                'name_id' => 'Belajar Cepat',
                'description_en' => 'Completed a course in record time',
                'description_id' => 'Menyelesaikan course dalam waktu singkat',
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                'color' => 'bg-teal-100 text-teal-600',
                'requirement_type' => 6,
                'requirement_value' => 1,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(
                ['slug' => $achievement['slug']],
                $achievement
            );
        }
    }
}
