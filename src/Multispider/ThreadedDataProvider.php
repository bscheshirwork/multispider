<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 16.10.16
 * Time: 16:38
 */

/**
 * Поставщик данных для потоков
 */
namespace Multispider;

class ThreadedDataProvider extends \Threaded
{
    /**
     * @var \SplQueue
     */
    private $taskQueue;

    /**
     * DataProvider constructor.
     * @param TaskData[] $data
     * @param int $multiplier
     */
    public function __construct($data, int $multiplier)
    {
        $multiplier > 0 || $multiplier = 1;
        $taskQueue = $this->getTaskQueue();
        foreach ($data as $element) {
            for ($i = 0; $i < $multiplier; $i++) {
                $taskQueue->enqueue($element);
            }
        }
        $this->taskQueue = $taskQueue;
    }

    /**
     * @param TaskData $task
     */
    private function enqueue(TaskData $task)
    {
        //не дружит с прямой работой(
        $taskQueue = $this->getTaskQueue();
        $taskQueue->enqueue($task);
        $this->taskQueue = $taskQueue;
    }

    /**
     * Dequeues a node from the queue
     * @link http://php.net/manual/en/splqueue.dequeue.php
     * @return mixed The value of the dequeued node.
     */
    private function dequeue(): TaskData
    {
        $taskQueue = $this->getTaskQueue();
        $value = $taskQueue->dequeue();
        $this->taskQueue = $taskQueue;
        return $value;
    }

    /**
     * Checks whether the doubly linked list is empty.
     * @link http://php.net/manual/en/spldoublylinkedlist.isempty.php
     * @return bool whether the doubly linked list is empty.
     */
    public function isEmpty(): bool
    {
        return $this->taskQueue->isEmpty();
    }

    /**
     * Переходим к следующему элементу и возвращаем его
     *
     * @return mixed
     */
    public function getNext(): TaskData
    {
        if ($this->isEmpty())
            return null;
        return $this->dequeue();
    }

    /**
     * @return \SplQueue
     */
    private function getTaskQueue(): \SplQueue
    {
        return $this->taskQueue ?? new \SplQueue();
    }
}