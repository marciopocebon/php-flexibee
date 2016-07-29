<?php
/**
 * FlexiPeeHP - Objekt účetního období.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  (C) 2015,2016 Spoje.Net
 */

namespace Test\FlexiPeeHP;

use FlexiPeeHP\UcetniObdobi;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-04-27 at 17:32:10.
 */
class UcetniObdobiTest extends FlexiBeeRWTest
{
    /**
     * @var UcetniObdobi
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new UcetniObdobi;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers FlexiPeeHP\UcetniObdobi::createYearsFrom
     */
    public function testCreateYearsFrom()
    {
        //Načíst stávající roky
        $fbyears = $this->object->getColumnsFromFlexibee(['kod'], null, 'kod');
        $years   = [];
        foreach ($fbyears as $fbyear) {
            if (is_numeric($fbyear['kod'])) {
                $years[] = $fbyear['kod'];
            }
        }
        asort($years);
        $firstyear = current($years);
        $testyear  = $firstyear - 2;


        //Založit další dva předcházející roky
        $this->object->createYearsFrom($testyear, $testyear + 1);

        //Znovu přečíst roky z FlexiBee
        $newfbyears = $this->object->getColumnsFromFlexibee(['kod'], null, 'kod');
        $newyears   = [];
        foreach ($newfbyears as $newfbyear) {
            if (is_numeric($newfbyear['kod'])) {
                $newyears[$newfbyear['kod']] = $newfbyear['kod'];
            }
        }

        //Byly požadované roky založeny ?
        $this->assertArrayHasKey($testyear, $newyears);
        $this->assertArrayHasKey($testyear + 1, $newyears);

        //Zkusit založit již existující období
        $wrong = $this->object->createYearsFrom(date('Y'));
        $this->assertEquals('false', $wrong[0]['success'],
            'current year does not exist ?');
    }

}
