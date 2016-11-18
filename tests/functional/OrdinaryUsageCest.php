<?php


class OrdinaryUsageCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    /**
     * run as init
     * @param FunctionalTester $I
     */
    public function ordinaryInitRunTest(FunctionalTester $I)
    {
        $I->runInit();
        $I->seeShellOutputMatches('|Done for \d+\.\d+ seconds|');
        $I->dontSeeInShellOutput('help: use \'--add\' command to schedule eraser');
    }

    /**
     * run as add schedule eraser
     * @param FunctionalTester $I
     */
    public function ordinaryAddRunTest(FunctionalTester $I)
    {
        $I->runAdd('/home/user/testFolder/ \'*1366x768*.[png\|css]\'');
        $I->seeShellOutputMatches('|Done for \d+\.\d+ seconds|');
        $I->dontSeeInShellOutput('help: use \'--add\' command to schedule eraser');
        $I->seeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
    }

    /**
     * run as schedule runner (May be dangerous! ~/tesFolder under attack!)
     * @param FunctionalTester $I
     */
    public function ordinaryRunRunTest(FunctionalTester $I)
    {
        $I->dontSeeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
        $I->runAdd('/home/user/testFolder/ \'*1366x768*.[png\|css]\'');
        $I->seeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
        $I->runRun();
        $I->seeShellOutputMatches('|Done for \d+\.\d+ seconds|');
        $I->dontSeeInShellOutput('help: use \'--add\' command to schedule eraser');
        $I->dontSeeInDatabase('multispider', ['path' =>'/home/user/testFolder/', 'mask' => '*1366x768*.[png\|css]']);
    }
}
