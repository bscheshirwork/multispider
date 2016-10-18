<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 16.10.16
 * Time: 16:39
 */
/**
 * MyWorker тут используется, чтобы расшарить провайдер и лог между экземплярами работы.
 */
class WorkerServiceShared extends Worker
{
    /**
     * @var ThreadedDataProvider
     */
    private $provider;

    /**
     * @var ThreadedLog $log
     */
    private $log;

    /**
     * @param ThreadedDataProvider $provider
     * @param ThreadedLog $log
     */
    public function __construct(ThreadedDataProvider $provider, ThreadedLog $log)
    {
        $this->provider = $provider;
        $this->log = $log;
    }

    /**
     * Вызывается при отправке в Pool.
     */
    public function run()
    {
        // В этом примере нам тут делать ничего не надо
    }

    /**
     * Возвращает провайдера
     *
     * @return ThreadedDataProvider
     */
    public function getProvider(): ThreadedDataProvider
    {
        return $this->provider;
    }

    /**
     * @return ThreadedLog
     */
    public function getLog(): ThreadedLog
    {
        return $this->log;
    }
}