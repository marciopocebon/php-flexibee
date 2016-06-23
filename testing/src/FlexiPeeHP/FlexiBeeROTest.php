<?php

namespace Test\FlexiPeeHP;

use FlexiPeeHP\FlexiBeeRO;

/**
 * Class used to test Object To Array Conversion
 */
class objTest extends \stdClass
{
    /**
     * Simple Item
     * @var integer
     */
    public $item = 1;

    /**
     * Array item
     * @var array
     */
    public $arrItem = ['a', 'b' => 'c'];

    /**
     * Simple method
     * 
     * @return boolean
     */
    public function method()
    {
        return true;
    }
}

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-05-04 at 10:08:36.
 */
class FlexiBeeROTest extends \Test\Ease\BrickTest
{
    /**
     * @var FlexiBeeRO
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @covers FlexiPeeHP\FlexiBeeRO::__construct
     */
    protected function setUp()
    {
        $this->object            = new FlexiBeeRO();
        $this->object->evidence  = 'c';
        $this->object->prefix    = '';
        $this->object->company   = '';
        $this->object->nameSpace = 'companies';
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::curlInit
     */
    public function testCurlInit()
    {
        $this->object->curlInit();
        $this->assertTrue(is_resource($this->object->curl));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::processInit
     */
    public function testProcessInit()
    {
        $this->object->processInit(['id' => 1]);
        $this->assertEquals(1, $this->object->getDataValue('id'));
        if (!is_null($this->object->evidence) && $this->object->evidence != 'test') {
            $firstID = $this->object->getColumnsFromFlexibee('id',
                ['limit' => 1]);
            $this->object->processInit((int) current($firstID));
            $this->assertNotEmpty($this->object->__toString());
        }
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::setEvidence
     */
    public function testSetEvidence()
    {
        $this->object->setEvidence('nastaveni');
        $this->assertEquals('nastaveni', $this->object->evidence);
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::object2array
     */
    public function testObject2array()
    {
        $this->assertNull($this->object->object2array(new \stdClass()));
        $this->assertEquals(
            [
            'item' => 1,
            'arrItem' => ['a', 'b' => 'c']
            ]
            , $this->object->object2array(new objTest()));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::objectToID
     */
    public function testObjectToID()
    {
        $id = \Ease\Sand::randomNumber(1, 9999);
        $this->object->setMyKey($id);
        $this->assertEquals([$id], $this->object->objectToID([$this->object]));

        $this->object->setDataValue('kod', 'TEST');
        $this->assertEquals('code:TEST',
            $this->object->objectToID($this->object));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::performRequest
     */
    public function testPerformRequest()
    {

        if (!is_null($this->object->evidence) && $this->object->evidence != 'test') {
            $json = $this->object->performRequest($this->object->evidence.'.json');
            if (array_key_exists('message', $json)) {
                $this->assertArrayHasKey('@version', $json);
            } else {
                $this->assertArrayHasKey('company', $json);
            }
        } else {
            $this->object->evidence  = 'c';
            $this->object->prefix    = '';
            $this->object->company   = '';
            $this->object->nameSpace = 'companies';
            $json                    = $this->object->performRequest();
            $this->assertArrayHasKey('company', $json);

            $xml = $this->object->performRequest(null, 'GET', 'xml');
            $this->assertArrayHasKey('company', $xml);
        }

        $err = $this->object->performRequest('error.json');
        $this->assertArrayHasKey('success', $err);
        $this->assertEquals('false', $err['success']);
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::setAction
     */
    public function testSetAction()
    {
        $this->assertTrue($this->object->setAction('none'));
        $this->object->actionsAvailable = [];
        $this->assertFalse($this->object->setAction('none'));
        $this->object->actionsAvailable = ['copy'];
        $this->assertFalse($this->object->setAction('none'));
        $this->assertTrue($this->object->setAction('copy'));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::getLastInsertedId
     * @depends testInsertToFlexiBee
     */
    public function testGetLastInsertedId()
    {
        $this->assertNotEmpty($this->object->getLastInsertedId());
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::xml2array
     */
    public function testXml2array()
    {
        $xml = '<card xmlns="http://businesscard.org">
   <name>John Doe</name>
   <title>CEO, Widget Inc.</title>
   <email>john.doe@widget.com</email>
   <phone>(202) 456-1414</phone>
   <logo url="widget.gif"/>
   <a><b>c</b></a>
 </card>';

        $data = ['name' => 'John Doe', 'title' => 'CEO, Widget Inc.', 'email' => 'john.doe@widget.com',
            'phone' => '(202) 456-1414', 'logo' => '', 'a' => [['b' => 'c']]];


        $this->assertEquals($data, $this->object->xml2array($xml));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::disconnect
     *
     * @depends testPerformRequest
     * @depends testLoadFlexiData
     * @depends testGetFlexiRow
     * @depends testGetFlexiData
     * @depends testLoadFromFlexiBee
     * @depends testInsertToFlexiBee
     * @depends testIdExists
     * @depends testRecordExists
     * @depends testGetColumnsFromFlexibee
     * @depends testSearchString
     */
    public function testDisconnect()
    {
        $this->object->disconnect();
        $this->assertNull($this->object->curl);
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::__destruct
     * @depends testDisconnect
     */
    public function test__destruct()
    {
        $this->markTestSkipped();
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::loadFlexiData
     * @todo   Implement testLoadFlexiData().
     */
    public function testLoadFlexiData()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::getFlexiRow
     */
    public function testGetFlexiRow()
    {
        $this->object->getFlexiRow(0);
        $this->object->getFlexiRow(1);
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::getFlexiData
     */
    public function testGetFlexiData()
    {
        if (is_null($this->object->evidence) || ($this->object->evidence == 'test')) {
            $this->object->evidence  = 'c';
            $this->object->prefix    = '';
            $this->object->company   = '';
            $this->object->nameSpace = 'companies';
            $flexidata               = $this->object->getFlexiData();
            $this->assertArrayHasKey('company', $flexidata);
        } else {
            $flexidata = $this->object->getFlexiData();
            $this->assertArrayHasKey(0, $flexidata);
            $this->assertArrayHasKey('id', $flexidata[0]);
            $filtrered = $this->object->getFlexiData(null,
                key($flexidata[0])." = ".current($flexidata[0]));
            $this->assertArrayHasKey(0, $filtrered);
            $this->assertArrayHasKey('id', $filtrered[0]);
        }
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::loadFromFlexiBee
     */
    public function testLoadFromFlexiBee()
    {
        $this->object->loadFromFlexiBee();
        $this->object->loadFromFlexiBee(222);
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::jsonizeData
     */
    public function testJsonizeData()
    {
        $this->assertEquals('{"'.$this->object->nameSpace.'":{"@version":"1.0","'.$this->object->evidence.'":{"key":"value"}}}',
            $this->object->jsonizeData(['key' => 'value']));
        $this->object->setAction('copy');
        $this->assertEquals('{"'.$this->object->nameSpace.'":{"@version":"1.0","'.$this->object->evidence.'":{"key":"value"},"'.$this->object->evidence.'@action":"copy"}}',
            $this->object->jsonizeData(['key' => 'value']));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::idExists
     * @todo   Implement testIdExists().
     */
    public function testIdExists()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::recordExists
     */
    public function testRecordExists()
    {
//        $this->assertTrue($this->object->recordExists([]));
//        $this->assertFalse($this->object->recordExists([]));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::getColumnsFromFlexibee
     * @todo   Implement testGetColumnsFromFlexibee().
     */
    public function testGetColumnsFromFlexibee()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::getKod
     */
    public function testGetKod()
    {

        $this->assertEquals('CODE',
            $this->object->getKod([$this->object->myKeyColumn => 'code']));

        $testString[$this->object->nameColumn] = 'Fish clamp -  Úchytka pro instalaci samonosných kabelů '
            .'(3.5 mm)';
        $code0                                 = $this->object->getKod($testString);
        $this->assertEquals('FISHCLAMPUCHYTKAPR', $code0);
        $code1                                 = $this->object->getKod($testString,
            false);
        $this->assertEquals('FISHCLAMPUCHYTKAPR', $code1);
        $code2                                 = $this->object->getKod($testString);
        $this->assertEquals('FISHCLAMPUCHYTKAPR1', $code2);
        $this->object->setData($testString);
        $code3                                 = $this->object->getKod();
        $this->assertEquals('FISHCLAMPUCHYTKAPR2', $code3);

        $this->assertEquals('TEST',
            $this->object->getKod([$this->object->nameColumn => 'test']));

        $this->assertEquals('TEST1', $this->object->getKod('test'));

        $this->assertEquals('TEST2', $this->object->getKod(['kod' => 'test']));
        $this->assertEquals('NOTSET', $this->object->getKod(['kod' => '']));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::logResult
     */
    public function testLogResult()
    {
        $this->object->cleanMessages();
        $success = json_decode('{"winstrom":{"@version":"1.0","success":"true",'
            .'"stats":{"created":"0","updated":"1","deleted":"0","skipped":"0"'
            .',"failed":"0"},"results":[{"id":"1","request-id":"ext:SōkoMan.item'
            .':5271","ref":"/c/spoje_net_s_r_o_1/skladovy-pohyb/1.json"}]}}');
        $this->object->logResult(current($this->object->object2array($success)),
            'http://test');

        $this->assertArrayHasKey('info', $this->object->getStatusMessages(true));

        $error = json_decode('{"winstrom":{"@version":"1.0","success":"false",'
            .'"stats":{"created":"0","updated":"0","deleted":"0","skipped":"0"'
            .',"failed":"0"},"results":[{"errors":[{"message":"cz.winstrom.'
            .'service.WSBusinessException: Zadaný kód není unikátní.\nZadaný'
            .' kód není unikátní."}]}]}}');
        $this->object->logResult(current($this->object->object2array($error)));
        $this->assertArrayHasKey('error', $this->object->getStatusMessages(true));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::flexiUrl
     */
    public function testFlexiUrl()
    {
        $this->assertEquals("a eq 1 and b eq 'foo'",
            $this->object->flexiUrl(['a' => 1, 'b' => 'foo'], 'and'));
        $this->assertEquals("a eq 1 or b eq 'bar'",
            $this->object->flexiUrl(['a' => 1, 'b' => 'bar'], 'or'));
        $this->assertEquals("a eq true or b eq false",
            $this->object->flexiUrl(['a' => true, 'b' => false], 'or'));
        $this->assertEquals("a is null and b is not null",
            $this->object->flexiUrl(['a' => null, 'b' => '!null'], 'and'));
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::__toString
     * @expectedException \Exception
     */
    public function test__toString()
    {

        $identifer = 'ext:test:123';
        $this->object->setDataValue('id', $identifer);
        $this->assertEquals($identifer, (string) $this->object);

        $code = 'test';
        $this->object->setDataValue('kod', $code);
        $this->assertEquals('code:'.$code, (string) $this->object);

        $this->object->dataReset();
        $this->object->__toString();
    }

    /**
     * @covers FlexiPeeHP\FlexiBeeRO::draw
     */
    public function testDraw($whatWant = NULL)
    {
        $this->object->setDataValue('kod', 'test');
        $this->assertEquals('code:test', $this->object->draw());
    }
}