<?php

/**
 * User: Z.kc <https://github.com/zkc226>
 * Date: 2017-01-17 16:02
 */

namespace zkc\DaemonTools;

class DaemonFunc
{

    private $callable;
    private $is_exit = false;

    /**
     * @param callable $callable 主函数, 循环体, 返回false结束运行
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function stop()
    {
        $this->is_exit = true;
    }

    /**
     * 运行
     *
     * @param int   $loop_times 执行次数, 0: 无限, >0 执行指定次数后结束
     * @param float $interval   执行间隔(秒), 0: 无限, >0 执行指定次数后结束
     */
    public function run($loop_times = 0, $interval = 0.0)
    {
        // 监听退出信号
        SignalHandler::addExitHanlder([$this, 'stop']);
        declare(ticks=1);

        $_loop = 0;

        while (!$this->is_exit) {
            if ($loop_times > 0) {
                $_loop++;
                if ($_loop > $loop_times) {
                    break;
                }
            }

            $ret = call_user_func($this->callable);
            if ($ret === false) {
                break;
            }
            if ($interval > 0) {
                usleep($interval * 1000);
            }
        }
    }

}