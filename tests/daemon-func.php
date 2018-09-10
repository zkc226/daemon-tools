<?php

use zkc\DaemonTools\DaemonFunc;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * 一个消费redis日志的例子
 */

$get_redis = function () {
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);

    return $redis;
};

/**
 * return false 退出程序
 */
$func = function () use ($get_redis) {

    $redis = $get_redis();

    try {

        $log = $redis->blPop('logs', 10);
        if (empty($log)) {
            sleep(2);

            return;
        }

        $logs = [$log];

        for ($i = 0; $i < 50; $i++) {
            $_log = $redis->lPop('logs');
            if (empty($_log)) {
                break;
            }
            $logs[] = $_log;
        }

        foreach ($logs as $log) {
            // do something...
        }

        unset($logs);

        return;

    } finally {
        if ($redis) {
            $redis->close();
        }
    }

};


try {
    $daemon = new DaemonFunc($func);
    $daemon->run();
} finally {
}