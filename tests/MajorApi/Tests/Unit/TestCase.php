<?php

namespace MajorApi\Tests\Unit;

use \PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{

    /** @var string */
    protected $fixtureDir = '';

    /** @var integer */
    protected $mockApplicationId = 96;

    public function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../../../../app/fixtures/');
    }

    public function getPostgresMockBuilder()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor();
    }

    public function getTwigMockBuilder()
    {
        return $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor();
    }

    public function getPostgresMock()
    {
        return $this->getPostgresMockBuilder()->getMock();
    }

    public function getTwigMock()
    {
        return $this->getTwigMockBuilder()->getMock();
    }

}
