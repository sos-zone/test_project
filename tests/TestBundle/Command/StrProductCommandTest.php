<?php

namespace Tests\TestBundle\Command;

use TestBundle\Command\StrProductCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class StrProductCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new StrProductCommand());

        $command = $application->find('action:execute');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'  => $command->getName(),

            // pass arguments to the helper
            'filePath' => '~/www/test_project/web/data/stock.csv',
            '--testMode' => 'test',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains(file_get_contents('~/www/test_project/web/data/stockTest.txt'), $output);
    }
}