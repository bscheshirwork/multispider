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
namespace Multispider;

class WorkerServiceShared extends \Worker
{
    /**
     * @var Cli
     */
    private $app;

    /**
     * @param Cli $app
     */
    public function __construct(Cli $app)
    {
        $this->app = $app;
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
        return $this->app->service('provider');
    }

    /**
     * @return ThreadedLog
     */
    public function getLog(): ThreadedLog
    {
        return $this->app->service('log');
    }
}