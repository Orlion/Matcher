<?php

namespace Orlion\Pattern\Nfa;


class NodeFactory
{
    private $nfaStates;

    public function __construct()
    {
        $this->nfaStates = 0;
    }

    public function getNode():Node
    {
        $node = new Node($this->nfaStates++);
        return $node;
    }
}