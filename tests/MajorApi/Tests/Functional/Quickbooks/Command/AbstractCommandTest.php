<?php

namespace MajorApi\Tests\Functional\Quickbooks\Command;

use MajorApi\Quickbooks\Command\AbstractCommand;
use MajorApi\Quickbooks\IppClient;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

/**
 * @group FunctionalTests
 */
class AbstractCommandTest extends TestCase
{

    public function testGettingIppClientRequiresIppApplication()
    {
        $command = $this->getCommand();

        $this->assertFalse($command->hasApplication());
        $this->assertNull($command->getIppClient());
    }

    public function testGettingIppClient()
    {
        $postgres = Registry::getPostgres();
        $twig = Registry::getTwig();
        $application = $this->getIppApplicationFixture();

        $command = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Command\AbstractCommand',
            [$postgres, $twig, $application['id'], 0, 0, uniqid(), uniqid()]
        );

        $ippClient = $command->getIppClient();

        $this->assertTrue($command->hasApplication());
        $this->assertInstanceOf('MajorApi\Quickbooks\IppClient', $ippClient);
    }

    public function testGettingParserRequiresParserClassToExist()
    {
        $command = $this->getCommand();
        $parser = $command->getParser('InvalidParser', '');

        $this->assertNull($parser);
    }

    public function testGettingParser()
    {
        $command = $this->getCommand();
        $parser = $command->getParser('ItemQueryParser', '');

        $this->assertInstanceOf('MajorApi\Quickbooks\Parser\Ipp\ItemQueryParser', $parser);
    }

    private function getCommand()
    {
        $application = $this->getApplicationFixture();

        $command = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Command\AbstractCommand',
            [Registry::getPostgres(), Registry::getTwig(), $application['id'], 0, 0, uniqid(), uniqid()]
        );

        return $command;
    }

}
