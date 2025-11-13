<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $taskCount = 0;

        $taskTitles = [
            'Design wireframes and mockups',
            'Setup development environment',
            'Implement authentication system',
            'Create database schema',
            'Build API endpoints',
            'Frontend component development',
            'User testing and feedback',
            'Performance optimization',
            'Documentation and code comments',
            'Security testing and fixes',
            'Deploy to staging environment',
            'Client review and approval',
            'Bug fixes and refinements',
            'Database migration',
            'API integration testing',
            'Responsive design implementation',
            'Code review and refactoring',
            'Load testing',
            'Deployment to production',
            'Post-launch monitoring',
        ];

        $statuses = ['pending', 'in_progress', 'completed', 'on_hold'];
        $priorities = ['low', 'medium', 'high', 'critical'];

        foreach ($projects as $project) {
            $employees = User::where('company_id', $project->company_id)
                ->whereIn('role', ['admin', 'employee'])
                ->get();

            $tasksPerProject = $taskCount < 30 ? min(3, 30 - $taskCount) : 0;

            for ($i = 0; $i < $tasksPerProject && $taskCount < 30; $i++) {
                $assignedTo = $employees->count() > 0 ? $employees->random()->id : null;

                Task::create([
                    'project_id' => $project->id,
                    'assigned_to' => $assignedTo,
                    'created_by' => $project->created_by,
                    'title' => $taskTitles[$i % count($taskTitles)],
                    'description' => fake()->paragraph(2),
                    'status' => $statuses[$i % 4],
                    'priority' => $priorities[$i % 4],
                    'due_date' => now()->addDays(fake()->numberBetween(7, 30)),
                    'estimated_hours' => fake()->numberBetween(2, 40),
                    'sort_order' => $i + 1,
                    'notes' => fake()->paragraph(1),
                ]);
                $taskCount++;
            }

            if ($taskCount >= 30) {
                break;
            }
        }

        $this->command->info($taskCount . ' tasks seeded successfully!');
    }
}
