<?php

namespace Test\FlexiPeeHP;

use \FlexiPeeHP\Status;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-10-27 at 23:57:50.
 */
class StatusTest extends FlexiBeeROTest
{
    /**
     * @var Status
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new \FlexiPeeHP\Status();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        
    }

    public function testConstructor()
    {
        $classname = get_class($this->object);
        // Get mock, without the constructor being called
        $mock      = $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mock->__construct();
    }

    /**
     * @covers FlexiPeeHP\Status::getFlexiData
     */
    public function testGetFlexiData()
    {
        $this->assertArrayHasKey('version', $this->object->getFlexiData());
    }

    /**
     * @covers FlexiPeeHP\Status::getData
     */
    public function testGetData()
    {
        $this->assertArrayHasKey('licenseName', $this->object->getData());
    }

    /**
     * @covers FlexiPeeHP\Status::unifyResponseFormat
     */
    public function testUnifyResponseFormat()
    {
        $this->assertEquals(['success' => 'false'],
            $this->object->unifyResponseFormat(['success' => 'false']));
        $this->assertEquals(['version' => 'ok'],
            $this->object->unifyResponseFormat(['status' => ['version' => 'ok']]));
    }
}
