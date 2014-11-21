<?php

use Infogram\BaseString;

class BaseStringTest extends \PHPUnit_Framework_TestCase
{
    public function testBaseString_shouldProduceValidString()
    {
        $baseString = BaseString::compute('post', 
                'http://infogram.local.com:1337/service/groceries',
                array(
                    'fruit' => 'apple',
                    'vegetable' => 'cucumber',
                    'salad' => 'green'));
        $this->assertEquals("POST&http%3A%2F%2Finfogram.local.com%3A1337%2Fservice%2Fgroceries&fruit%3Dapple%26salad%3Dgreen%26vegetable%3Dcucumber", $baseString);
    }
}
