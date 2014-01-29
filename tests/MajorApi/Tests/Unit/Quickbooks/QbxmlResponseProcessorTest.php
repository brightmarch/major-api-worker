<?php

namespace MajorApi\Tests\Unit\Quickbooks;

use MajorApi\Quickbooks\QbxmlResponseProcessor;
use MajorApi\Tests\Unit\TestCase;

use \ReflectionMethod;

/**
 * @group UnitTests
 */
class QbxmlResponseProcessorTest extends TestCase
{

    public function testGettingUnprocessedQuickbooksQueues()
    {
        $mockUnprocessedQuickbooksQueues = [
            [
                'id' => mt_rand(1, 100),
                'persister' => 'ItemQueryPersister'
            ]
        ];

        $mockPostgres = $this->getPostgresMockBuilder()
            ->setMethods(['fetchAll'])
            ->getMock();

        $mockPostgres->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue($mockUnprocessedQuickbooksQueues));

        $qbxmlResponseProcessor = new QbxmlResponseProcessor(
            $mockPostgres,
            $this->mockApplicationId
        );

        $this->assertFalse($qbxmlResponseProcessor->hasUnprocessedQuickbooksQueues());

        $unprocessedQuickbooksQueues = $qbxmlResponseProcessor->getUnprocessedQuickbooksQueues();

        $this->assertTrue($qbxmlResponseProcessor->hasUnprocessedQuickbooksQueues());
        $this->assertEquals($mockUnprocessedQuickbooksQueues[0]['id'], $unprocessedQuickbooksQueues[0]['id']);
    }

    public function testGettingParserRequiresPersisterClassToExist()
    {
        $qbxmlResponseProcessor = new QbxmlResponseProcessor(
            $this->getPostgresMock(),
            $this->mockApplicationId
        );

        $method = new ReflectionMethod($qbxmlResponseProcessor, 'getParser');
        $method->setAccessible(true);
        $parser = $method->invoke($qbxmlResponseProcessor, 'InvalidPersister');

        $this->assertNull($parser);
    }

    public function testGettingParser()
    {
        $qbxmlResponseProcessor = new QbxmlResponseProcessor(
            $this->getPostgresMock(),
            $this->mockApplicationId
        );

        $method = new ReflectionMethod($qbxmlResponseProcessor, 'getParser');
        $method->setAccessible(true);
        $parser = $method->invoke($qbxmlResponseProcessor, 'HostQueryPersister');

        $this->assertInstanceOf('MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser', $parser);
    }

    public function testGettingPersisterRequiresPersisterClassToExist()
    {
        $mockParser = $this->getMockBuilder('MajorApi\Quickbooks\Parser\AbstractQbxmlParser')
            ->disableOriginalConstructor()
            ->getMock();

        $qbxmlResponseProcessor = new QbxmlResponseProcessor(
            $this->getPostgresMock(),
            $this->mockApplicationId
        );

        $method = new ReflectionMethod($qbxmlResponseProcessor, 'getPersister');
        $method->setAccessible(true);
        $persister = $method->invoke($qbxmlResponseProcessor, 'InvalidPersister', $mockParser);

        $this->assertNull($persister);
    }

    public function testGettingPersister()
    {
        $mockParser = $this->getMockBuilder('MajorApi\Quickbooks\Parser\AbstractQbxmlParser')
            ->disableOriginalConstructor()
            ->getMock();

        $qbxmlResponseProcessor = new QbxmlResponseProcessor(
            $this->getPostgresMock(),
            $this->mockApplicationId
        );

        $method = new ReflectionMethod($qbxmlResponseProcessor, 'getPersister');
        $method->setAccessible(true);
        $persister = $method->invoke($qbxmlResponseProcessor, 'HostQueryPersister', $mockParser);

        $this->assertInstanceOf('MajorApi\Quickbooks\Persister\HostQueryPersister', $persister);
    }

    public function testMarkingQuickbooksQueueProcessed()
    {
        $mockQuickbooksQueueId = 96;

        $mockPostgres = $this->getPostgresMockBuilder()
            ->setMethods(['update'])
            ->getMock();

        $mockPostgres->expects($this->once())
            ->method('update')
            ->will($this->returnValue(1));

        $qbxmlResponseProcessor = new QbxmlResponseProcessor(
            $mockPostgres,
            $this->mockApplicationId
        );

        $method = new ReflectionMethod($qbxmlResponseProcessor, 'markQuickbooksQueueProcessed');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($qbxmlResponseProcessor, $mockQuickbooksQueueId));
    }

}
