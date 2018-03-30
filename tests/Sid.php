<?php
namespace HalimonAlexander\Sid\Tests;

include_once 'fixtures/testSid.php';

use HalimonAlexander\Sid\Tests\Fixtures\testSid;
use HalimonAlexander\Sid\Exception\SidRuntimeException;
use PHPUnit\Framework\TestCase;

class Sid extends TestCase
{
    private $sid = [];
    private $testData = [
    "TEST_VALUE_1" => 1,
    "TEST_VALUE_2" => 2,
    "TEST_VALUE_3" => 3,
    "TEST_VALUE_4" => 4,
  ];

    public function setUp()
    {
    }

    public function testNxException()
    {
        $nxSid = testSid::nx();
        $this->assertEquals($nxSid, 5);

        $this->expectException('SidRuntimeException');
        testSid::getNameById($nxSid);
    }
  
    public function testList()
    {
        $fullList = testSid::getList(true);
        $this->assertEquals($this->testData, $fullList);

        $notFullList = testSid::getList();
        $this->assertArrayNotHasKey('TEST_VALUE_3', $notFullList);
    }
}
