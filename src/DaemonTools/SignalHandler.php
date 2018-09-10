<?php

namespace zkc\DaemonTools;

/**
 * User: Z.kc <https://github.com/zkc226>
 * Date: 2017-01-09 16:37
 */
class SignalHandler
{

    static $isDeclare = false;

    public static function addHandler($signo, $callback)
    {
        if (!self::$isDeclare) {
            self::$isDeclare = true;
            //信号处理需要注册ticks才能生效，这里务必注意
            //PHP5.4以上版本就不再依赖ticks了
            declare(ticks=1);
        }
        //echo ':' . $signo . PHP_EOL;
        $ret = pcntl_signal($signo, $callback);

        return $ret;
    }

    public static function addExitHanlder($callback)
    {
        $signos = array(
            SIGUSR1,
            SIGHUP,
            SIGQUIT,
            // SIGINT mac 下导致 segmentation fault
            SIGINT,
            //SIGKILL,
            SIGTERM,
            SIGSYS,
        );
        foreach ($signos as $signo) {
            SignalHandler::addHandler($signo, $callback);
        }
    }


}