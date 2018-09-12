<?php
/**
 * Created by PhpStorm.
 * User: zkc
 * Date: 2018-09-12
 * Time: 15:35
 */

namespace zkc\DaemonTools;


abstract class Process
{

    public $pid;
    public $ppid;
    public $name = 'daemon-process';

    public abstract function run();

    public function isToStop()
    {
        return $this->isToExit;
    }

    private $isToExit = false;

    public function installSignal()
    {
        $that = $this;
        SignalHandler::addExitHanlder(function () use ($that) {
            // echo "child to exit\n";
            $that->isToExit = true;
        });
    }


}