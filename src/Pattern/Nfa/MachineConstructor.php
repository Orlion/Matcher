<?php

namespace Orlion\Pattern\Nfa;


use Orlion\Pattern\Lexer;

class MachineConstructor
{
    private $lexer;
    private $nodeFactory;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->nodeFactory = new NodeFactory();
    }

    public function build()
    {
        $nfa = new Nfa();

        $this->expr($nfa);

        return $nfa;
    }

    private function expr(Nfa $nfa)
    {

        while ($this->lexer->advance())
        {
            $this->char($nfa);
        }
    }

    private function char(Nfa $nfa)
    {
        if ($this->lexer->getCurrentToken() !== Lexer::TOKEN_MAP['CHAR'])
        {
            return false;
        }

        $node = $this->nodeFactory->getNode();
        $node->setEdge($this->lexer->getCurrentToken());
        $node->setCharSet([]);

        $nfa->start = $node;

        $node = $this->nodeFactory->getNode();
        $node->setEdge(Node::EMPTY);
        $nfa->end = $node;

        $this->lexer->advance();

        return true;
    }
}