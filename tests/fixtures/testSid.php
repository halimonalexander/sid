<?php
namespace HalimonAlexander\Sid\Tests\Fixtures;

class testSid extends \HalimonAlexander\Sid\Sid{
  const TEST_VALUE_1 = 1;
  const TEST_VALUE_2 = 2;
  const _TEST_VALUE_3 = 3;
  const TEST_VALUE_4 = 4;
  const __INVALID_VALUE = 5; // This Sid must be ignored
}