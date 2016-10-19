<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 16.10.16
 * Time: 16:40
 */

/**
 * это задача, которая может выполняться параллельно
 */
namespace Multispider;

class ThreadedFileEraser extends \Threaded
{

    public function run()
    {
        do {
            /** @var TaskData $taskData */
            $taskData = null;

            /** @var ThreadedDataProvider $provider */
            $provider = $this->worker->getProvider();

            /** @var ThreadedLog $log */
            $log = $this->worker->getLog();

            // Синхронизируем получение данных
            $provider->synchronized(function ($provider) use (&$taskData) {
                /** @var ThreadedDataProvider $provider */
                $taskData = $provider->getNext();
            }, $provider);

            if ($taskData === null) {
                continue;
            }

            try {
                // todo: разбор регулярки, FilesystemIterator, применение ограничений и прочие проверки тут.
                // Или же просто возложим всю работу с удалением на плечи системы, где всё это есть.
                // С вероятностью выстрела в ногу.
                // и со своими приколами. Для общего теста временно так и поступим:
                $shellCommand = 'rm ' . $taskData->getPath() . $taskData->getMask() .'';
                $spellCheckFail = preg_filter([' /', '..', './', ';'], ['err', 'err', 'err', 'err'], [$taskData->getPath(), $taskData->getMask()]) || preg_last_error();
                if ($spellCheckFail)
                    throw new \Exception('Wrong format of path or mask');
                else
                    $shellResult = `$shellCommand`;
                $message = 'Work is done! ' . $shellResult;
            } catch (\Exception $e) {
                echo 'Remove error' . $taskData->getPath() . $taskData->getMask() . PHP_EOL;
                $message = 'Work fail! ' . $taskData->getPath() . $taskData->getMask();
            }

            $log->synchronized(function ($log) use (&$message) {
                /** @var ThreadedLog $log */
                $log->log($message);
            }, $log);

        } while ($taskData !== null);
    }

}