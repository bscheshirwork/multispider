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
    public function log($message)
    {
        $template = "{time}s : {mesg}";
        $time = sprintf("%.6f", microtime(true) - $this->start);
        $log = $this->log;
        // add records to the log
        $log->info(strtr($template, [
            '{time}' => $time,
            '{mesg}' => $message,
        ]));
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