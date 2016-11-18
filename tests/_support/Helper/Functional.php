<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    /**
     * Initial init command
     */
    public function runInit(){
        $this->getModule('Cli')->runShellCommand('php src/entrypoint.php --init', false);
    }

    /**
     * Initial add task(s) command
     * @param string $paramString
     */
    public function runAdd(string $paramString){
        $this->getModule('Cli')->runShellCommand('php src/entrypoint.php --add ' . $paramString, false);
    }

    /**
     * Initial run command
     */
    public function runRun(){
        $this->getModule('Cli')->runShellCommand('php src/entrypoint.php --run', false);
    }

    public function runRunRun(){
        // RU-U-U-U-UN!!!
    }
}
