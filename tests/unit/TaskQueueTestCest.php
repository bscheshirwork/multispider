<?php


class TaskQueueTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    /**
     * for test fail connection
     * @param UnitTester $I
     */
    public function tryConstructFailTest(UnitTester $I)
    {
        $I->wantTo('construct without connection settings throw error');
        $cli = Multispider\Cli::app();
        $cli->serviceAdd('log', new class {
            public function error($message){
                throw new \PDOException($message);
            }
        });
        $cli->serviceAdd('options');
        $I->expectException(\PDOException::class, function (){
            new Multispider\TaskQueue;
        });
        Multispider\Cli::shutdown();
   }

    /**
     * for test connection
     * @param UnitTester $I
     */
    public function tryConstructTest(UnitTester $I)
    {
        $I->wantTo('construct with data storing it to database');
        $cli = Multispider\Cli::app();
        $cli->serviceAdd('log', new class {
            public function error($message){
                throw new \PDOException($message);
            }
        });
        $I->dontSeeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
        $td = new Multispider\TaskData('/home/user/testFolder/', '*1366x768*.[png\|css]');
        $tq = new Multispider\TaskQueue([$td]);
        $I->seeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
        Multispider\Cli::shutdown();
    }
}
