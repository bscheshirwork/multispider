<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 19.10.16
 * Time: 15:19
 */

namespace Multispider;


class Cli
{
    private static $app;

    private $services = [];

    /**
     * Создать консольное приложение. Конфигурируется только единожды
     * @param array $options
     * @return Cli
     */
    public static function app(array $options = []): Cli
    {
        return self::$app = self::$app ?? new self($options);
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * Cli constructor.
     * @param $options array
     */
    private function __construct(array $options)
    {
        $this->serviceAdd('options', $options = new \ArrayObject([
            'threads' => $options['threads'] ?? 2,
            'multiplier' => $options['multiplier'] ?? 1,
            'logDir' => $options['logDir'] ?? '/multispider/logs',
        ], \ArrayObject::ARRAY_AS_PROPS));

        // Лог
        $this->serviceAdd('log', new ThreadedLog(null, $options->logDir));
    }

    /**
     * Return service. Return default if not exist or null
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function service($name, $default = null)
    {
        return $this->services[$name] ?? $default;
    }

    /**
     * Store service
     * @param $name
     * @param null $service
     * @return null
     */
    public function serviceAdd($name, $service = null)
    {
        return $this->services[$name] = $service;
    }

    /**
     * Add a rule
     * @internal param $path_1
     * @internal param $mask_1
     * ...
     * @internal param $path_N
     * @internal param $mask_N
     */
    public function add()
    {
        $num = func_num_args();
        $raw = func_get_args();
        $taskDataList = [];
        switch (true) {
            case $num == 1:
                //todo: json_decode($raw, true);
                $this->exit(1, 'Command add: wrong number of parameters (json not supported)');
                break;
            case !($num % 2):
                for ($i = 0; $i < $num; $i += 2) {
                    $taskDataList[] = new TaskData($raw[$i], $raw[$i + 1]);
                }
                break;
            default:
                $this->exit(1, 'Command add: wrong number of parameters');
        }
        $this->serviceAdd('taskDataList', $taskDataList);
    }

    public function run()
    {
        $this->service('log')->log('RUN');

        $data = $this->service('taskDataList', []);
        $multiplier = $this->service('options')->multiplier;
        // Создадим провайдер. Этот сервис читает данные из очереди
        $provider = $this->serviceAdd('provider', new ThreadedDataProvider($data, $multiplier));

        // Лог
        $log = $this->service('log');

        $threads = $this->service('options')->threads;

        // Создадим пул воркеров
        $pool = $this->service('pool', new \Pool($threads, 'Multispider\WorkerServiceShared', [$this]));

        // В нашем случае потоки сбалансированы.
        // Поэтому тут хорошо создать столько потоков, сколько процессов в нашем пуле.
        $workers = $threads;
        for ($i = 0; $i < $workers; $i++) {
            $pool->submit(new ThreadedFileEraser());
        }

        $pool->shutdown();

    }

    public function exit(int $code, string $message = '')
    {
        echo $message . PHP_EOL;
        exit($code);
    }
}