<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Discussion',
                'description' => 'General topics and discussions about learning',
                'color' => '#6366f1',
                'order' => 1,
            ],
            [
                'name' => 'Course Help',
                'description' => 'Get help with specific courses and assignments',
                'color' => '#059669',
                'order' => 2,
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Technical issues and platform support',
                'color' => '#dc2626',
                'order' => 3,
            ],
            [
                'name' => 'Study Groups',
                'description' => 'Form study groups and collaborate',
                'color' => '#7c3aed',
                'order' => 4,
            ],
            [
                'name' => 'Job & Career',
                'description' => 'Career advice and job opportunities',
                'color' => '#ea580c',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::create($category);
        }
    }
}