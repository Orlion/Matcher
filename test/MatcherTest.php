<?php


namespace Test;

use Orlion\Matcher\Matcher;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    public function testTest()
    {
        $pattern = 'a';
        $subject = 'a';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'ab';
        $subject = 'ab';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'a[bc]o';
        $subject = 'abc';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'a[^bc]o';
        $subject = 'aoo';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'a[bc]*o';
        $subject = 'accco';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'a[bc]?o';
        $subject = 'accco';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'a.o';
        $subject = 'a0o';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));

        $pattern = 'abc|def';
        $subject = 'abc';
        $matcher = new Matcher($pattern);
        $this->assertEquals(preg_match('/' . $pattern . '/', $subject), $matcher->test($subject));
    }
}