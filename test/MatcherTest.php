<?php


namespace Test;

use Orlion\Matcher\Matcher;

require_once '../vendor/autoload.php';

class MatcherTest
{
    public static function testTest()
    {
        $pattern = 'aa+';
        $subject = 'aa';
        $matcher = new Matcher($pattern);
        var_dump($matcher->test($subject));
    }
}

MatcherTest::testTest();

