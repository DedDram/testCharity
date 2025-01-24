<?php

namespace Database\Seeders;

use App\Models\CharityProject;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DonationsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем все проекты
        $projects = CharityProject::all();

        // Генерируем 50 пожертвований
        for ($i = 0; $i < 50; $i++) {
            // Выбираем случайный проект
            $project = $projects->random();

            Donation::create([
                'charity_project_id' => $project->id,
                'donation_date' => Carbon::now()->subDays(rand(0, 30)),
                'amount' => rand(100, 10000),
                'comment' => 'Комментарий к пожертвованию ' . ($i + 1),
            ]);
        }
    }
}
