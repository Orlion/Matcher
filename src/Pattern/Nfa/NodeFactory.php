<?php

namespace Orlion\Matcher\Pattern\Nfa;


class NodeFactory
{
    private $nfaStates;

    public function __construct()
    {
        $this->nfaStates = 1;
    }

    public function getNode():Node
    {
        $node = new Node($this->nfaStates++);
        return $node;
    }
}