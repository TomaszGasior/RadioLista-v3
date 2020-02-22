<?php

namespace Deployer;

require 'recipe/symfony4.php';

set('repository', 'git@github.com:TomaszGasior/RadioLista-v3.git');
set('branch', function(){ return runLocally('git describe master --abbrev=0'); });

set('env', ['APP_ENV' => 'prod']);
set('shared_dirs', ['var/log', 'var/sessions', 'var/lock']);
set('shared_files', ['.env.local.php']);
set('keep_releases', -1);

inventory('deploy.yaml');

desc('Clear PHP opcache');
task('deploy:clear_opcache', 'cachetool opcache:reset --web --web-path {{public_dir}} --web-url http://{{domain}}');
after('deploy:symlink', 'deploy:clear_opcache');

desc('Enable maintenance mode');
task('maintenance:enable', 'touch var/lock/maintenance.lock');

desc('Disable maintenance mode');
task('maintenance:disable', 'rm -f var/lock/maintenance.lock');
