<?php

use AspectMock\Test as test;
use Robo\Config;
class PHPServerTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Development\loadTasks;
    use \Robo\TaskSupport;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $process;

    protected function _before()
    {
        $this->process = test::double('Symfony\Component\Process\Process', [
            'run' => false,
            'start' => false,
            'getOutput' => 'Hello world',
            'getExitCode' => 0
        ]);
        test::double('Robo\Task\Development\PhpServer', ['getOutput' => new \Symfony\Component\Console\Output\NullOutput()]);
        $this->setTaskAssembler(new \Robo\TaskAssembler(Config::logger()));
    }

    public function testServerBackgroundRun()
    {
        $this->taskServer('8000')->background()->run();
        $this->process->verifyInvoked('start');
    }

    public function testServerRun()
    {
        $this->taskServer('8000')->run();
        $this->process->verifyInvoked('run');
    }

    public function testServerCommand()
    {
        if (strtolower(PHP_OS) === 'linux') {
            $expectedCommand = 'exec php -S 127.0.0.1:8000 -t web';
        } else {
            $expectedCommand = 'php -S 127.0.0.1:8000 -t web';
        }

        verify(
            $this->taskServer('8000')
                ->host('127.0.0.1')
                ->dir('web')
                ->getCommand()
        )->equals($expectedCommand);
    }

}
