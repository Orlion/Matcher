<?php

namespace Test;

use Orlion\Matcher;

class MatcherTest
{
    public static function testTest()
    {
        $pattern = '/a/';
        $subject = 'a';
        $matcher = new Matcher($pattern);
        if ($matcher->test($subject) !== preg_match($pattern, $subject))
        {
            echo "error";
        }
    }
}

MatcherTest::testTest();