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
class ThreadedLog extends Threaded
{
    private $start;

    /**
     * ThreadedLog constructor.
     * @param $start
     */
    public function __construct($start = null)
    {
        $this->start = $start ?? microtime(true);
    }

    /**
     * Запись лога (можно расширить при наличии времени - писать в файл, писать кусками, ротация файлов, прочее)
     * @param $message
     */
    public function log($message)
    {
        $template = "{date} {time}s : {mesg}" . PHP_EOL;
        $date = date("Y-m-d H:i:s");
        $time = sprintf("%.6f", microtime(true) - $this->start);
        echo strtr($template, [
            '{date}' => $date,
            '{time}' => $time,
            '{mesg}' => $message,
        ]);
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