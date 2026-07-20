<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            PathologySeeder::class,
            InclusionCriteriaSeeder::class,
            ExclusionCriteriaSeeder::class,
            StepSeeder::class,
            SessionChecklistSeeder::class,
            FollowUpSeeder::class,
            ApplicationAreaSeeder::class,
            QuestionnaireSeeder::class,
            CenterSeeder::class,
            CrmUserSeeder::class,
            EquipmentSeeder::class,
        ]);
    }
}
