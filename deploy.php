<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project configuration
set('application', 'personal-blog');
set('repository', getenv('DEPLOY_REPOSITORY') ?: 'git@github.com:user/personal-blog.git');
set('keep_releases', 5);
set('git_tty', false);

// PHP binary path (adjust per server)
set('bin/php', 'php');

// Shared files and directories (persist across releases)
add('shared_files', ['.env']);
add('shared_dirs', [
    'storage',
    'storage/app/content-repo',  // Persist git clone across deployments
]);

// Writable directories
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

// Production host (configure via environment or edit directly)
host('production')
    ->setHostname(getenv('DEPLOY_HOST') ?: 'example.com')
    ->setRemoteUser(getenv('DEPLOY_USER') ?: 'forge')
    ->setDeployPath(getenv('DEPLOY_PATH') ?: '/home/forge/personal-blog');

// Restart queue workers after symlink swap (new code takes effect)
after('deploy:symlink', 'artisan:queue:restart');

// Unlock deployment on failure
after('deploy:failed', 'deploy:unlock');

// Custom health check task
task('health:check', function () {
    $url = get('health_check_url', 'http://localhost/health');
    $response = run("curl -sf {$url}");
    $data = json_decode($response, true);

    if (! $data || ($data['status'] ?? '') !== 'ok') {
        throw new \RuntimeException(
            'Health check failed: '.($response ?: 'no response')
        );
    }

    writeln('<info>Health check passed</info>');
})->desc('Verify application health after deployment');

// Run health check after deployment
after('deploy:symlink', 'health:check');

// Custom task: run content sync after deployment
task('content:sync', function () {
    run('{{bin/php}} {{release_or_current_path}}/artisan content:sync');
})->desc('Sync content from git repository');
