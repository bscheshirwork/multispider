<?php


class CliOrdinaryRunTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    /**
     * run as init
     * @param UnitTester $I
     */
    public function ordinaryInitRunTest(UnitTester $I)
    {
        $argc = 1;
        $argv[1] = ['--init'];
        require_once dirname(__DIR__) . '/src/entrypoint.php';

//        $I->seeShellOutputMatches('|Done for \d+\.\d+ seconds|');
//        $I->dontSeeInShellOutput('help: use \'--add\' command to schedule eraser');
    }

    /**
     * run as add schedule eraser
     * @param UnitTester $I
     */
//    public function ordinaryAddRunTest(UnitTester $I)
//    {
//        $I->runAdd('/home/user/testFolder/ \'*1366x768*.[png\|css]\'');
//        $I->seeShellOutputMatches('|Done for \d+\.\d+ seconds|');
//        $I->dontSeeInShellOutput('help: use \'--add\' command to schedule eraser');
//        $I->seeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
//    }
//
//    /**
//     * run as schedule runner (May be dangerous! ~/tesFolder under attack!)
//     * @param UnitTester $I
//     */
//    public function ordinaryRunRunTest(UnitTester $I)
//    {
//        $I->dontSeeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
//        $I->runAdd('/home/user/testFolder/ \'*1366x768*.[png\|css]\'');
//        $I->seeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
//        $I->runRun();
//        $I->seeShellOutputMatches('|Done for \d+\.\d+ seconds|');
//        $I->dontSeeInShellOutput('help: use \'--add\' command to schedule eraser');
//        $I->dontSeeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
//    }
}
