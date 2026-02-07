<?php

return [
    'name' => env('PORTFOLIO_NAME', 'Your Name'),
    'title' => env('PORTFOLIO_TITLE', 'Software Developer'),
    'bio' => [
        'intro' => 'A passionate software developer who loves building things for the web.',
        'extended' => 'Write a more detailed bio here about your background and experience. This could include your journey into tech, your approach to problem-solving, what drives your passion for development, and any career highlights or achievements you want to share with visitors.',
    ],
    'photo' => null, // Path to profile photo, e.g., 'images/profile.jpg'
    'interests' => [
        'Open source software',
        'System design',
        'Technical writing',
        'Continuous learning',
        'Developer tooling',
        'Web performance',
    ],
    'tech_stack' => [
        'Languages' => [
            ['name' => 'PHP', 'note' => '5+ years'],
            ['name' => 'JavaScript', 'note' => '5+ years'],
            ['name' => 'TypeScript', 'note' => '3+ years'],
            ['name' => 'SQL', 'note' => '5+ years'],
            ['name' => 'HTML/CSS', 'note' => '6+ years'],
        ],
        'Frameworks & Libraries' => [
            ['name' => 'Laravel', 'note' => '4+ years'],
            ['name' => 'Vue.js', 'note' => '3+ years'],
            ['name' => 'TailwindCSS', 'note' => '3+ years'],
            ['name' => 'Alpine.js', 'note' => '2+ years'],
        ],
        'Tools & Platforms' => [
            ['name' => 'Git', 'note' => 'Daily driver'],
            ['name' => 'Docker', 'note' => 'Development & production'],
            ['name' => 'Linux', 'note' => 'Primary OS'],
            ['name' => 'PostgreSQL', 'note' => 'Preferred database'],
            ['name' => 'Nginx', 'note' => 'Web server'],
        ],
        'Specializations' => [
            ['name' => 'API Design', 'note' => 'REST & GraphQL'],
            ['name' => 'DevOps', 'note' => 'CI/CD pipelines'],
            ['name' => 'Performance', 'note' => 'Optimization & caching'],
        ],
    ],
    'social' => [
        'github' => env('SOCIAL_GITHUB', 'https://github.com/yourusername'),
        'twitter' => env('SOCIAL_TWITTER', 'https://twitter.com/yourusername'),
        'linkedin' => env('SOCIAL_LINKEDIN', ''),
        'email' => env('SOCIAL_EMAIL', ''),
    ],
];
