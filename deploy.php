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

foreach (Deployer::get()->hosts as $host) {
    $host->set('hostname', $host->getHostname());
    $host->set('domain', $host->getHostname());
}

after('deploy:failed', 'deploy:unlock');

desc('Save version name');
task('deploy:version', '({{bin/git}} describe --exact-match HEAD 2> /dev/null || {{bin/git}} rev-parse --short HEAD) > version');
after('deploy:update_code', 'deploy:version');

desc('Build assets');
task('deploy:build_assets', '{{bin/npm}} install; {{bin/npm}} run build');
after('deploy:vendors', 'deploy:build_assets');

desc('Clear PHP opcache');
task('deploy:clear_opcache', 'cachetool opcache:reset --web --web-path {{public_dir}} --web-url http://{{domain}}');
after('deploy:symlink', 'deploy:clear_opcache');
after('rollback', 'deploy:clear_opcache');

desc('Enable maintenance mode');
task('maintenance:enable', 'touch var/lock/maintenance.lock');

desc('Disable maintenance mode');
task('maintenance:disable', 'rm -f var/lock/maintenance.lock');
