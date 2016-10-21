<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 18.10.16
 * Time: 3:37
 */

/**
 * Логирование действий воркеров, вызывается синхронизированно
 */
namespace Multispider;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ThreadedLog extends \Threaded
{
    private $start;

    private $log;

    /**
     * ThreadedLog constructor.
     * @param $start
     * @param null $logDir
     */
    public function __construct($start = null, $logDir = null)
    {
        $this->start = $start ?? microtime(true);
        // create a log channel

        $log = new Logger('main');
        $log->pushHandler(new StreamHandler($logDir . '/main.log', Logger::INFO));
        $this->log = $log;
    }

    /**
     * Запись лога (можно расширить при наличии времени - форматирование средствами Monolog)
     * @param $message
     */
    public function info($message)
    {
        $log = $this->log;
        $log->info(sprintf("%.6fs : %s", microtime(true) - $this->start, $message));
    }

    /**
     * Запись ошибки
     * @param $message
     */
    public function error($message){
        $log = $this->log;
        $log->error(sprintf("%.6fs : %s", microtime(true) - $this->start, $message));
    }

    /**
     * @param mixed $start
     * @return ThreadedLog
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }
}