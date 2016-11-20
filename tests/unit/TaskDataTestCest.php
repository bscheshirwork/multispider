<?php


class TaskDataTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests

    /**
     * for test default necessary params
     * @param UnitTester $I
     */
    public function tryConstructDefaultTest(UnitTester $I)
    {
        $I->wantTo('construct without path fill it default');
        $td = new Multispider\TaskData;
        $I->assertNotEmpty($td->getPath());
        $I->assertEquals('~/.', $td->getPath());
        $I->assertEmpty($td->getMask());
    }

    /**
     * for test default necessary params
     * @param UnitTester $I
     */
    public function tryConstructTest(UnitTester $I)
    {
        $I->wantTo('construct with path and mask fill it');
        $td = new Multispider\TaskData('/home/user/testFolder/', '*1366x768*.[png\|css]');
        $I->assertEquals('/home/user/testFolder/', $td->getPath());
        $I->assertEquals('*1366x768*.[png\|css]', $td->getMask());
    }

    /**
     * for test getter-setter
     * @param UnitTester $I
     */
    public function tryGetterSetterTest(UnitTester $I)
    {
        $I->wantTo('getter and setter');
        $td = new Multispider\TaskData;
        $I->assertNotEmpty($td->getPath());
        $I->assertEmpty($td->getMask());
        $td->setPath('/home/user/testFolder/');
        $td->setMask('*1366x768*.[png\|css]');
        $I->assertEquals('/home/user/testFolder/', $td->getPath());
        $I->assertEquals('*1366x768*.[png\|css]', $td->getMask());
    }


}
