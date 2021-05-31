<?php
require_once __DIR__.'/autoload.php';

function bootstrap(): void
{
    $kernel = new AppKernel('test', true);
    $kernel->boot();

    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);


    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'cache:clear',
        '--no-warmup' => 1,
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:create',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:schema:create',
    ]));

    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '-n'
    ]));

    $kernel->shutdown();
}

bootstrap();

