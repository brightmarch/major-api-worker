<?php

namespace MajorApi\Tests\Unit\Quickbooks;

use MajorApi\Quickbooks\IppRequestProcessor;
use MajorApi\Tests\Unit\TestCase;

use \ReflectionMethod;

/**
 * @group UnitTests
 */
class IppRequestProcessorTest extends TestCase
{

    public function testGettingQuickbooksQueue()
    {
        $mockObjectId = mt_rand(1, 100);
        $mockQuickbooksQueue = [
            'id' => mt_rand(1, 100),
            'command' => 'ItemQueryCommand',
            'persister' => 'ItemQueryPersister'
        ];

        $mockPostgres = $this->getPostgresMockBuilder()
            ->setMethods(['fetchAssoc'])
            ->getMock();

        $mockPostgres->expects($this->once())
            ->method('fetchAssoc')
            ->will($this->returnValue($mockQuickbooksQueue));

        $ippRequestProcessor = new IppRequestProcessor(
            $mockPostgres,
            $this->getTwigMock(),
            $this->mockApplicationId,
            $mockObjectId,
            $mockQuickbooksQueue['id'],
            uniqid(),
            uniqid()
        );

        $this->assertFalse($ippRequestProcessor->hasQuickbooksQueue());

        $quickbooksQueue = $ippRequestProcessor->getQuickbooksQueue();

        $this->assertTrue($ippRequestProcessor->hasQuickbooksQueue());
        $this->assertEquals($mockQuickbooksQueue['id'], $quickbooksQueue['id']);
    }

    public function testGettingCommandRequiresCommandClassToExist()
    {
        $mockObjectId = mt_rand(1, 100);
        $mockQuickbooksQueueId = mt_rand(1, 100);

        $ippRequestProcessor = new IppRequestProcessor(
            $this->getPostgresMock(),
            $this->getTwigMock(),
            $this->mockApplicationId,
            $mockObjectId,
            $mockQuickbooksQueueId,
            uniqid(),
            uniqid()
        );

        $method = new ReflectionMethod($ippRequestProcessor, 'getCommand');
        $method->setAccessible(true);
        $command = $method->invoke($ippRequestProcessor, 'InvalidCommand');

        $this->assertNull($command);
    }

    public function testGettingCommand()
    {
        $mockObjectId = mt_rand(1, 100);
        $mockQuickbooksQueueId = mt_rand(1, 100);

        $ippRequestProcessor = new IppRequestProcessor(
            $this->getPostgresMock(),
            $this->getTwigMock(),
            $this->mockApplicationId,
            $mockObjectId,
            $mockQuickbooksQueueId,
            uniqid(),
            uniqid()
        );

        $method = new ReflectionMethod($ippRequestProcessor, 'getCommand');
        $method->setAccessible(true);
        $command = $method->invoke($ippRequestProcessor, 'ItemQueryCommand');

        $this->assertInstanceOf('MajorApi\Quickbooks\Command\ItemQueryCommand', $command);
    }

}
