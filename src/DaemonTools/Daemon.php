<?php

/**
 * User: Z.kc <https://github.com/zkc226>
 * Date: 2017-01-17 10:49
 */

namespace zkc\DaemonTools;

class Daemon
{

    private $parentPid;
    private $childrenPid;
    private $supportPcntl = false;

    public function __construct($name = '')
    {
        $this->supportPcntl = extension_loaded('pcntl');

        if (!$this->supportPcntl) {
            echo 'need pcntl extension' . PHP_EOL;
            exit;
        }
        SignalHandler::addExitHanlder([$this, 'stop']);

        if (!empty($name)) {
            @cli_set_process_title($name);
        }

        $this->parentPid = getmypid();
    }

    public function getChildrenPid()
    {
        return $this->childrenPid;
    }

    public function getParentPid()
    {
        return $this->parentPid;
    }

    /**
     * fork 子进程
     *
     * @param Process $process
     *
     * @return int                     -1: 失败, >0 子进程编号
     */
    public function fork(Process $process)
    {
        if ($this->supportPcntl) {
            $pid = pcntl_fork();
        } else {
            $pid = 0;
        }

        if ($pid == -1) {
            // fork fail

        } elseif ($pid == 0) {
            declare(ticks=1);

            // echo $pid . PHP_EOL;
            // child process
            $childPid      = posix_getpid();
            $process->pid  = $childPid;
            $process->ppid = posix_getppid();

            if (!empty($process->name)) {
                @cli_set_process_title($process->name);
            }

            $process->installSignal();
            $process->run();

            exit(0);
        } else {
            $this->childrenPid[$pid] = time();
        }

        return $pid;
    }

    public function stop()
    {
        // echo "exit\n";
        if (!empty($this->childrenPid)) {
            foreach ($this->childrenPid as $cpid => $cval) {
                posix_kill($cpid, SIGINT);
            }
        }
    }

    public function wait()
    {
        if (!$this->supportPcntl) {
            return true;
        }
        $num = 0;

        pcntl_signal_dispatch();

        while (true && count($this->childrenPid) > 0) {
            if (count($this->childrenPid) <= $num) {
                break;
            }

            foreach ($this->childrenPid as $cpid => $cval) {
                $status = 0;
                pcntl_waitpid($cpid, $status, WNOHANG);
            }

            foreach ($this->childrenPid as $cpid => $cval) {
                if (!posix_kill($cpid, 0)) {
                    unset($this->childrenPid [$cpid]);
                }
            }

            if (count($this->childrenPid) <= $num) {
                break;
            }

            usleep(500000);
        }
    }


}