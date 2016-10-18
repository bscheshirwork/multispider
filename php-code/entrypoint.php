<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 16.10.16
 * Time: 16:36
 */

require_once 'WorkerServiceShared.php';
require_once 'ThreadedFileEraser.php';
require_once 'ThreadedDataProvider.php';
require_once 'ThreadedLog.php';
require_once 'TaskData.php';

//чтение параметров
switch ($argc) {
    case 1:
        $executeOnly = true;
        $taskDataList = [];
        break;
    case 4:
        $executeOnly = false;
        switch ($argv[1]) {
            case '--add':
                $taskDataList = [new TaskData($argv[2], $argv[3])];
                break;
            default:
                echo "help: use '--add' command to schedule eraser " . PHP_EOL . "for example" . PHP_EOL . " --add /home/user/testFolder/ '*1366x768*.[png|css]'" . PHP_EOL;
                exit(1);
        }
        break;
    default:
        echo "help: use '--add' command to schedule eraser " . PHP_EOL . "for example" . PHP_EOL . " --add /home/user/testFolder/ '*1366x768*.[png|css]'" . PHP_EOL;
        exit(1);
}


//чтение настроек
try {
    $iniFileName = 'settings.ini';
    if (file_exists($iniFileName) && is_readable($iniFileName))
        $settings = parse_ini_file($iniFileName, INI_SCANNER_TYPED);
    else
        throw new Exception('IO error.');

    //настройки по умолчанию
    $threads = $settings['threads'] ?? 2;
    $multiplier = $settings['multiplier'] ?? 1;

} catch (Exception $exception) {
    echo $exception->getMessage() . ' Please check settings.ini. The file exist and can be read?' . PHP_EOL;
    exit(1);
}

//todo: сохранение-загрузка - создать сервис базы


// Создадим провайдер. Этот сервис читает данные из очереди
$provider = new ThreadedDataProvider($taskDataList, $multiplier);

// Лог
$log = new ThreadedLog();

// Создадим пул воркеров
$pool = new Pool($threads, 'WorkerServiceShared', [$provider, $log]);

$start = microtime(true);

// В нашем случае потоки сбалансированы.
// Поэтому тут хорошо создать столько потоков, сколько процессов в нашем пуле.
$workers = $threads;
for ($i = 0; $i < $workers; $i++) {
    $pool->submit(new ThreadedFileEraser());
}

$pool->shutdown();

printf("Done for %.2f seconds" . PHP_EOL, microtime(true) - $start);