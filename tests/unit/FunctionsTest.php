<?php

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

    public function testExtractParameterFromUrl()
    {
        $name = 'testparameter';
        $value = uniqid();

        // test valid
        $url = 'https://this.ismy.url/with/path?para1=value+1&' . $name . '=' . $value . '&para2=value+2';
        $testValue = \I4code\JaAuth\extractParameterFromUrl($name, $url);
        $this->assertEquals($value, $testValue);

        // no code in query
        $url = 'https://this.ismy.url/with/path?para1=value+1&para2=value+2';
        $testValue = \I4code\JaAuth\extractParameterFromUrl($name, $url);
        $this->assertNull($testValue);

        // no query
        $url = 'https://this.ismy.url/with/path';
        $testValue = \I4code\JaAuth\extractParameterFromUrl($name, $url);
        $this->assertNull($testValue);

        // no query
        $url = 'pathonly';
        $testValue = \I4code\JaAuth\extractParameterFromUrl($name, $url);
        $this->assertNull($testValue);

    }

}
