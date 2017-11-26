<?php


namespace HalimonAlexander\Sid\Tests;
include_once 'fixtures/testSid.php';
use HalimonAlexander\Sid\Tests\Fixtures\testSid;
use PHPUnit\Framework\TestCase;

class Sid extends TestCase{

  private $sid = [];
  private $testData = [
    "TEST_VALUE_1" => 1,
    "TEST_VALUE_2" => 2,
    "TEST_VALUE_3" => 3,
    "TEST_VALUE_4" => 4,
  ];

  function setUp(){
  }

  function testNxException(){
    $nxSid = testSid::nx();
    $this->assertEquals($nxSid, 5);

    $this->expectException('\HalimonAlexander\Sid\Exception\SidRuntimeException');
    testSid::getNameById($nxSid);
  }
  
  function testList(){
    $fullList = testSid::getList(true);
    $this->assertEquals($this->testData, $fullList);

    $notFullList = testSid::getList();
    $this->assertArrayNotHasKey('TEST_VALUE_3', $notFullList);
  }

}