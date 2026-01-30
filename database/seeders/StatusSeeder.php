<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Backlog', 'sorting' => 0],
            ['name' => 'In Progress', 'sorting' => 1],
            ['name' => 'Review', 'sorting' => 2],
            ['name' => 'Done', 'sorting' => 3],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['name' => $status['name']],
                ['sorting' => $status['sorting']]
            );
        }
    }
}
