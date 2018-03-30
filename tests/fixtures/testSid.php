<?php
namespace HalimonAlexander\Sid\Tests\Fixtures;

use HalimonAlexander\Sid\Sid;

class testSid extends Sid
{
    const TEST_VALUE_1 = 1;
    
    const TEST_VALUE_2 = 2;
    
    const _TEST_VALUE_3 = 3;
    
    const TEST_VALUE_4 = 4;
    
    // This Sid must be ignored
    const __INVALID_VALUE = 5; 
}
