<?php

require_once __DIR__ . '/../vendor/autoload.php';

// important !!!!!!
declare(ticks=1);

$daemon = new \zkc\DaemonTools\Daemon();

class ProcessDemo extends \zkc\DaemonTools\Process
{

    public function run()
    {
        while (!$this->isToStop()) {
            sleep(1);
        }
        echo "exit";
    }
}


$daemon->fork(new ProcessDemo());

$daemon->wait();