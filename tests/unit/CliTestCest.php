<?php


class CliTestCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests

    /**
     * for test instance pattern
     * @param UnitTester $I
     */
    public function tryCliInstance(UnitTester $I){
        $I->wantTo('cli is instance');
        $I->expectException(\Exception::class, function (){
            try {
                /*
                 * Masquerade "new Multispider\Cli;"
                 */
                $class = 'Multispider\Cli';
                new $class;
                // new Multispider\Cli;
            }catch (Error $e){
                throw new Exception;
            }
        });
        $I->expectException(\Exception::class, function (){
            try {
                /*
                 * Masquerade "clone Multispider\Cli::app();"
                 */
                $cli = Multispider\Cli::app();
                $objectName = 'cli';
                clone $$objectName;
                // clone Multispider\Cli::app();
            }catch (Error $e){
                throw new Exception;
            }
        });
        $I->expectException(\Exception::class, function (){
            try {
                unserialize(serialize(Multispider\Cli::app()));
            }catch (Error $e){
                throw new Exception;
            }
        });
        /**
         * Asserts that two variables have the same type and value.
         * Used on objects, it asserts that two variables reference
         * the same object.
         */
        $I->assertSame(Multispider\Cli::app(), Multispider\Cli::app());
        Multispider\Cli::shutdown();
    }

    /**
     * for test fill as default all necessary params
     * @param UnitTester $I
     */
    public function tryCliConstructTest(UnitTester $I)
    {
        $I->wantTo('run cli without config fill default params');
        $cli = Multispider\Cli::app();
        $I->assertNotEmpty($cli->service('options'));
        $I->assertInstanceOf(ArrayAccess::class, $cli->service('options'));
        $options = $cli->service('options');
        $I->assertArrayHasKey('threads', $options);
        $I->assertArrayHasKey('multiplier', $options);
        $I->assertArrayHasKey('logDir', $options);
        $I->assertArrayHasKey('db', $options);
        $I->assertInstanceOf(ArrayAccess::class, $options->db);
        $db = $options->db;
        $I->assertArrayHasKey('connectionString', $db);
        $I->assertArrayHasKey('user', $db);
        $I->assertArrayHasKey('password', $db);
        $I->assertArrayHasKey('tableName', $db);
        $I->assertArrayHasKey('insertBlockSize', $db);
        $I->assertArrayHasKey('selectBlockSize', $db);

        $I->assertNotEmpty($cli->service('log'));
        $I->assertInstanceOf(\Multispider\ThreadedLog::class, $cli->service('log'));
        Multispider\Cli::shutdown();
        /**
         * Only first run accept config
         */
        Multispider\Cli::app(['someConfigService'=>new \ArrayObject(['someConfigItem'])]);
        $I->assertArrayNotHasKey('someConfigService', $cli->service('options'));
        $I->expectException(\Exception::class, function (){
            Multispider\Cli::app(['anotherConfigService'=>new \ArrayObject(['anotherConfigItem'])])->service('option')->anotherConfigService;
        });
    }

    /**
     * for test add services / read service
     * @param UnitTester $I
     */
    public function tryCliServiceAddTest(UnitTester $I){
        $I->wantTo('add services to cli app storage');
        $cli = Multispider\Cli::app();
        $I->assertEmpty($cli->service('exampleService'));
        $cli->serviceAdd('exampleService', new \ArrayObject(['property'=>'value']));
        $I->assertNotEmpty($cli->service('exampleService'));
        $I->assertInstanceOf(ArrayAccess::class, $cli->service('exampleService'));
        $cli->serviceAdd('exampleService');
        $I->assertEmpty($cli->service('exampleService'));
        $I->assertNotInstanceOf(ArrayAccess::class, $cli->service('exampleService'));
        $I->assertNotEmpty($cli->service('exampleService', new \ArrayObject(['anotherProperty'=>'anotherValue'])));
        $I->assertInstanceOf(ArrayAccess::class, $cli->service('exampleService', new \ArrayObject(['thirdProperty'=>'thirdValue'])));
        Multispider\Cli::shutdown();
    }

}
