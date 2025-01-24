<?php

namespace Database\Seeders;

use App\Models\CharityProject;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CharityProjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['draft', 'active', 'closed'];
        for($i = 1; $i <= 10; $i++){
            CharityProject::create([
                "name" => "Проект $i",
                "slug" => "proekt-$i",
                "short_description" => "<p>Краткое описание проекта $i.</p>",
                "status" => Arr::random($statuses),
                "launch_date" => Carbon::now()->addDays(rand(0, 30)),
                "additional_description" => "<p>Дополнительное описание проекта $i.</p>",
                "donation_amount" => $i * 1000,
                "sort_order" => $i,
            ]);
        }
    }
}
