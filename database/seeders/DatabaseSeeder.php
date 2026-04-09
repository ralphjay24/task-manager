<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $work = Project::query()->create(['name' => 'Work']);
        $home = Project::query()->create(['name' => 'Home']);

        $workTasks = ['Plan sprint', 'Review pull requests', 'Write documentation'];
        foreach ($workTasks as $index => $name) {
            Task::query()->create([
                'project_id' => $work->id,
                'name'       => $name,
                'priority'   => $index + 1,
            ]);
        }

        Task::query()->create([
            'project_id' => $home->id,
            'name'       => 'Buy groceries',
            'priority'   => 1,
        ]);
    }
}
