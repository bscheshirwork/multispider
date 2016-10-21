<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 16.10.16
 * Time: 16:36
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

$options = require_once __DIR__ . '/config.php';

$cliApp = Multispider\Cli::app($options);

$start = microtime(true);

//чтение параметров

switch (true) {
    case ($argc == 1):
    case ($argv[1] == '--run'):
        $cliApp->run();
        break;
    case ($argv[1] == '--init'):
        $cliApp->init();
        break;
    case ($argv[1] != '--add'):
    case ($argc == 2):
        $cliApp->exit(1, "help: use '--add' command to schedule eraser " . PHP_EOL . "for example" . PHP_EOL . " --add /home/user/testFolder/ '*1366x768*.[png|css]'");
        break;
    case !(($argc - 2) % 2):
        $cliApp->add(...array_slice($argv, 2));
        break;
    default:
        $cliApp->exit(1, "help: use '--add' command to schedule eraser " . PHP_EOL . "for example" . PHP_EOL . " --add /home/user/testFolder/ '*1366x768*.[png|css]'");
}

printf("Done for %.2f seconds" . PHP_EOL, microtime(true) - $start);