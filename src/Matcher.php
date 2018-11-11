<?php

namespace Orlion\Matcher;


use Orlion\Matcher\Exception\MatcherException;
use Orlion\Matcher\Pattern\Lexer;
use Orlion\Matcher\Pattern\Nfa\MachineConstructor;
use Orlion\Matcher\Pattern\Nfa\Interpreter;

class Matcher
{
    private $nfa;

    /**
     * 构造方法
     *
     * Matcher constructor.
     * @param string $pattern
     * @throws MatcherException
     */
    public function __construct(string $pattern)
    {
        $lexer = new Lexer($pattern);
        // 构建NFA
        $nfaMachineConstructor = new MachineConstructor($lexer);
        $this->nfa = $nfaMachineConstructor->build();
        if (is_null($this->nfa->startNode) || is_null($this->nfa->endNode)) {
            throw new MatcherException('正则表达式不能为空', 1);
        }
    }

    public function test(string $subject):bool
    {
        $nfaInterpreter = new Interpreter($this->nfa);
        return $nfaInterpreter->run($subject);
    }
}

