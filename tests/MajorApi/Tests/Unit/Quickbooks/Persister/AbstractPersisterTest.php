<?php

namespace MajorApi\Tests\Unit\Quickbooks\Persister;

use MajorApi\Tests\Unit\TestCase;

use \Exception;

/**
 * @group UnitTests
 */
class AbstractPersisterTest extends TestCase
{

    public function testPersistingXmlRequiresValidXml()
    {
        $mockApplicationId = mt_rand(1, 100);

        $mockPostgres = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $mockParser = $this->getMockBuilder('MajorApi\Quickbooks\Parser\AbstractParser')
            ->disableOriginalConstructor()
            ->setMethods(['load', 'getResultTag', 'initialize', 'parse'])
            ->getMock();

        $mockParser->expects($this->once())
            ->method('load')
            ->will($this->throwException(new Exception));

        $persister = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Persister\AbstractPersister',
            [$mockPostgres, $mockParser, $mockApplicationId]
        );

        $this->assertFalse($persister->persist());
    }

}
