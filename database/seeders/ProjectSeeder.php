<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Project 1: Personal Blog & Portfolio (active, featured)
        Project::create([
            'title' => 'Personal Blog & Portfolio',
            'slug' => 'personal-blog-portfolio',
            'short_description' => 'A Laravel-based personal blog with git-based publishing, syntax highlighting, and portfolio showcase.',
            'description' => <<<'DESC'
This is my personal blog and portfolio website, built from scratch using Laravel 12 and modern web technologies. It features a clean, minimalist design with the Rose Pine color theme and dark mode support.

The blog supports markdown-based content with Shiki syntax highlighting for code blocks, automatic reading time calculation, and a responsive design that works beautifully on all devices. Content is managed through a git-based workflow, where writing posts in a separate repository automatically syncs to the production site via webhooks.

The portfolio section showcases my projects with a masonry grid layout, status filtering, and detailed project pages with tech stack badges and external links.
DESC,
            'tech_stack' => ['Laravel', 'PHP', 'PostgreSQL', 'TailwindCSS', 'Alpine.js', 'Vite'],
            'status' => 'active',
            'is_featured' => true,
            'thumbnail' => null,
            'live_url' => 'https://example.com',
            'github_url' => 'https://github.com/user/personal-blog',
            'screenshots' => [],
            'sort_order' => 1,
        ]);

        // Project 2: Task Management API (active)
        Project::create([
            'title' => 'Task Management API',
            'slug' => 'task-management-api',
            'short_description' => 'RESTful API for task management with real-time updates and team collaboration.',
            'description' => <<<'DESC'
A comprehensive task management REST API built with Laravel, featuring real-time updates through WebSockets and team collaboration capabilities. The API supports creating workspaces, managing team members, assigning tasks, and tracking progress with activity logs.

Authentication is handled via JWT tokens with refresh rotation for security. The API includes rate limiting, comprehensive error handling, and detailed API documentation using Swagger/OpenAPI standards.
DESC,
            'tech_stack' => ['Laravel', 'PHP', 'Redis', 'WebSockets', 'Docker'],
            'status' => 'active',
            'is_featured' => false,
            'thumbnail' => null,
            'live_url' => null,
            'github_url' => 'https://github.com/user/task-api',
            'screenshots' => [],
            'sort_order' => 2,
        ]);

        // Project 3: E-Commerce Dashboard (completed)
        Project::create([
            'title' => 'E-Commerce Dashboard',
            'slug' => 'ecommerce-dashboard',
            'short_description' => 'Admin dashboard for e-commerce analytics with real-time sales tracking.',
            'description' => <<<'DESC'
A modern admin dashboard for e-commerce analytics, built with Vue.js and TypeScript. The dashboard provides real-time sales tracking, inventory management, and comprehensive analytics with interactive charts powered by Chart.js.

Features include order management, customer insights, product catalog administration, and automated reporting via email. The interface is fully responsive and built with TailwindCSS for rapid styling and customization.
DESC,
            'tech_stack' => ['Vue.js', 'TypeScript', 'Chart.js', 'TailwindCSS'],
            'status' => 'completed',
            'is_featured' => false,
            'thumbnail' => null,
            'live_url' => 'https://dashboard.example.com',
            'github_url' => 'https://github.com/user/ecommerce-dashboard',
            'screenshots' => [],
            'sort_order' => 3,
        ]);

        // Project 4: Open Source CLI Tool (in-progress)
        Project::create([
            'title' => 'Open Source CLI Tool',
            'slug' => 'cli-tool',
            'short_description' => 'Command-line tool for automating development workflows and project scaffolding.',
            'description' => <<<'DESC'
A work-in-progress CLI tool built with Go and Cobra, designed to automate common development workflows and project scaffolding tasks. The tool generates project structures, sets up CI/CD pipelines, and manages environment configurations across multiple frameworks.

Currently supports Laravel, Vue.js, and plain JavaScript projects, with plans to expand to additional frameworks. The tool is designed to be extensible through a plugin system.
DESC,
            'tech_stack' => ['Go', 'Cobra', 'SQLite'],
            'status' => 'in-progress',
            'is_featured' => false,
            'thumbnail' => null,
            'live_url' => null,
            'github_url' => 'https://github.com/user/cli-tool',
            'screenshots' => [],
            'sort_order' => 4,
        ]);

        // Project 5: Weather Widget (archived)
        Project::create([
            'title' => 'Weather Widget',
            'slug' => 'weather-widget',
            'short_description' => 'Embeddable weather widget using OpenWeatherMap API with customizable themes.',
            'description' => <<<'DESC'
A lightweight, embeddable weather widget that displays current conditions and forecasts using the OpenWeatherMap API. The widget supports multiple themes and can be customized through CSS variables.

This project is now archived as I've transitioned to using native weather integrations in my other projects. The codebase remains available for reference and as an example of API integration and widget development.
DESC,
            'tech_stack' => ['JavaScript', 'CSS', 'REST API'],
            'status' => 'archived',
            'is_featured' => false,
            'thumbnail' => null,
            'live_url' => null,
            'github_url' => 'https://github.com/user/weather-widget',
            'screenshots' => [],
            'sort_order' => 5,
        ]);

        // Project 6: Recipe Sharing Platform (completed, featured)
        Project::create([
            'title' => 'Recipe Sharing Platform',
            'slug' => 'recipe-sharing-platform',
            'short_description' => 'Social platform for sharing and discovering recipes with meal planning.',
            'description' => <<<'DESC'
A full-stack social platform for sharing and discovering recipes, built with Laravel and Vue.js. Users can create recipe collections, follow other cooks, and plan weekly meals using a drag-and-drop interface.

The platform features advanced search with filters for dietary restrictions, ingredients, and cooking time. Integration with Algolia provides instant search results. Image optimization and CDN delivery ensure fast load times for recipe photos.

Community features include ratings, comments, and the ability to save favorite recipes. Automated meal planning generates shopping lists based on selected recipes.
DESC,
            'tech_stack' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Redis', 'Algolia'],
            'status' => 'completed',
            'is_featured' => true,
            'thumbnail' => null,
            'live_url' => 'https://recipes.example.com',
            'github_url' => 'https://github.com/user/recipes',
            'screenshots' => [],
            'sort_order' => 6,
        ]);
    }
}
