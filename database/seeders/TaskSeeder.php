<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(StatusSeeder::class);

        $statusIds = Status::query()
            ->orderBy('sorting')
            ->pluck('id', 'name');

        if ($statusIds->isEmpty()) {
            return;
        }

        $tasks = [
            [
                'title' => 'Collect requirements',
                'description' => 'Align scope and success criteria with stakeholders.',
                'status' => 'Backlog',
                'sorting' => 0,
            ],
            [
                'title' => 'Design kanban layout',
                'description' => 'Define columns, card content, and drag rules.',
                'status' => 'Backlog',
                'sorting' => 1,
            ],
            [
                'title' => 'Build task model',
                'description' => 'Create migrations, relations, and seed data.',
                'status' => 'In Progress',
                'sorting' => 0,
            ],
            [
                'title' => 'Wire MoonShine resource',
                'description' => 'Connect kanban board with statuses and sorting.',
                'status' => 'In Progress',
                'sorting' => 1,
            ],
            [
                'title' => 'QA board drag/drop',
                'description' => 'Verify column moves and ordering updates.',
                'status' => 'Review',
                'sorting' => 0,
            ],
            [
                'title' => 'Publish demo',
                'description' => 'Share access details with the team.',
                'status' => 'Done',
                'sorting' => 0,
            ],
        ];

        foreach ($tasks as $task) {
            $statusId = $statusIds->get($task['status']);

            if ($statusId === null) {
                continue;
            }

            Task::updateOrCreate(
                [
                    'title' => $task['title'],
                    'status_id' => $statusId,
                ],
                [
                    'description' => $task['description'],
                    'sorting' => $task['sorting'],
                ]
            );
        }
    }
}
