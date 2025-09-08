<?php

namespace Deployer;

require 'recipe/symfony.php';

set('branch', function() {
    return runLocally('git describe master --abbrev=0');
});

set('env', ['APP_ENV' => 'prod']);
set('shared_dirs', ['var/log', 'var/sessions', 'var/lock']);
set('shared_files', ['.env.local.php']);
set('clear_paths', ['node_modules']);
set('keep_releases', -1);

import('deploy.yaml');

after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'deploy:clear_paths');

desc('Save version name');
task('deploy:version', function() {
    $version = runLocally('git describe master --abbrev=0') === get('target') ? get('target') : get('release_revision');
    run("echo '$version' > {{release_path}}/version");
});
after('deploy:update_code', 'deploy:version');

desc('Clear PHP opcache');
task('deploy:clear_opcache', function() {
    cd('{{release_path}}');
    run('cachetool opcache:reset --web --web-path {{public_dir}} --web-url http://{{domain}}');
});
after('deploy:symlink', 'deploy:clear_opcache');
after('rollback', 'deploy:clear_opcache');

desc('Enable maintenance mode');
task('maintenance:enable', function() {
    run('touch {{current_path}}/var/lock/maintenance.lock');
});

desc('Disable maintenance mode');
task('maintenance:disable', function() {
    run('rm -f {{current_path}}/var/lock/maintenance.lock');
});
