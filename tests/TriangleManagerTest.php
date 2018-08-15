<?php

use Triangle\TriangleManager;

class TriangleManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function testThreeTri()
    {
        $tri = new TriangleManager(1,2);
        $total = $tri->getCalculateTri();
        $this->assertEquals($total, 3);
    }

    public function testFourTri()
    {
        $tri = new TriangleManager(9,10);
        $total = $tri->getCalculateTri();
        $this->assertEquals($total, 4);
    }

    public function testManyTri()
    {
        $tri = new TriangleManager(1,10000);
        $total = $tri->getCalculateTri();
        $this->assertEquals($total, 83540657);
    }
/*
    public function testVeryManyTri()
    {
        $tri = new TriangleManager(1,1000000);
        $total = $tri->getCalculateTri();
        $this->assertEquals($total, -1);
    }
*/


}
