<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed managers
        $manager1 = User::create([
            'name' => 'Manager One',
            'email' => 'manager1@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::manager->value,
        ]);

        $manager2 = User::create([
            'name' => 'Manager Two',
            'email' => 'manager2@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::manager->value,
        ]);

        // Seed users
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::user->value,
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::user->value,
        ]);

        $user3 = User::create([
            'name' => 'User Three',
            'email' => 'user3@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::user->value,
        ]);

        // Seed tasks
        $task1 = Task::create([
            'title' => 'Design Homepage',
            'description' => 'Create a responsive homepage design.',
            'due_date' => '2025-09-25',
            'assignee_id' => $user1->id,
            'created_by_id' => $manager1->id,
        ]);

        $task2 = Task::create([
            'title' => 'Implement API',
            'description' => 'Develop the task management API.',
            'due_date' => '2025-09-30',
            'assignee_id' => $user1->id,
            'created_by_id' => $manager1->id,
        ]);

        $task3 = Task::create([
            'title' => 'Test Application',
            'description' => 'Perform unit and integration testing.',
            'due_date' => '2025-10-05',
            'assignee_id' => $user1->id,
            'created_by_id' => $manager1->id,
        ]);

        $task4 = Task::create([
            'title' => 'Design Database Schema',
            'description' => 'Create a scalable database schema.',
            'due_date' => '2025-09-28',
            'assignee_id' => $user2->id,
            'created_by_id' => $manager2->id,
        ]);

        $task5 = Task::create([
            'title' => 'Develop Authentication',
            'description' => 'Implement user authentication system.',
            'due_date' => '2025-10-02',
            'assignee_id' => $user2->id,
            'created_by_id' => $manager2->id,
        ]);

        $task6 = Task::create([
            'title' => 'Deploy Application',
            'description' => 'Deploy the application to production.',
            'due_date' => '2025-10-10',
            'assignee_id' => $user3->id,
            'created_by_id' => $manager2->id,
        ]);

        $task7 = Task::create([
            'title' => 'Write Documentation',
            'description' => 'Prepare user and developer documentation.',
            'due_date' => '2025-10-12',
            'assignee_id' => $user3->id,
            'created_by_id' => $manager1->id,
        ]);

        // Add dependencies
        $task2->dependencies()->attach($task1->id); // Implement API depends on Design Homepage
        $task3->dependencies()->attach([$task1->id, $task2->id]); // Test Application depends on both
        $task5->dependencies()->attach($task4->id); // Develop Authentication depends on Database Schema
        $task6->dependencies()->attach([$task3->id, $task5->id]); // Deploy Application depends on Test and Auth
        $task7->dependencies()->attach($task6->id); // Write Documentation depends on Deploy
    }
}