<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

require __DIR__ . '/bootstrap.php';

(function(){
    $kernel = new Kernel('test', true);
    $kernel->boot();

    $application = new Application($kernel);
    $application->setAutoExit(false);

    $application->run(new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--quiet' => '1',
        '--force' => '1',
    ]));
    $application->run(new ArrayInput([
        'command' => 'doctrine:database:create',
        '--quiet' => '1',
    ]));
    $application->run(new ArrayInput([
        'command' => 'doctrine:schema:create',
        '--quiet' => '1',
    ]));
    $application->run(new ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '--quiet' => '1',
        '--no-interaction' => '1',
    ]));

    $kernel->shutdown();
})();
