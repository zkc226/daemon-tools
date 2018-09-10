<?php

require_once __DIR__ . '/../vendor/autoload.php';

$daemon = new \zkc\DaemonTools\Daemon();

$daemon->fork(function () {
    while (1) {
        echo '123' . PHP_EOL;
        sleep(1);
    }
});

$daemon->wait();