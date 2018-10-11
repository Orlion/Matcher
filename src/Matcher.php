<?php

namespace Orlion;


use Orlion\Pattern\Lexer;
use Orlion\Pattern\Nfa\MachineConstructor;
use Orlion\Pattern\Nfa\Interpreter;

class Matcher
{
    private $nfa;

    public function __construct(string $pattern)
    {
        $lexer = new Lexer($pattern);
        // 构建NFA
        $nfaMachineConstructor = new MachineConstructor($lexer);
        $this->nfa = $nfaMachineConstructor->build();
    }

    public function test(string $subject):bool
    {
        $nfaInterpreter = new Interpreter($this->nfa);
        return $nfaInterpreter->interpret($subject);
    }
}