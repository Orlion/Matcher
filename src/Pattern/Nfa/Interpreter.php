<?php

namespace Orlion\Matcher\Pattern\Nfa;


class Interpreter
{
    private $nfa;

    public function __construct(Nfa $nfa)
    {
        $this->nfa = $nfa;
    }

    public function run(string $subject):bool
    {
        $nextNodes = [];
        $nextNodes[] = $this->nfa->startNode;

        $nextNodes = $this->closure($nextNodes);

        $len = strlen($subject);
        $index = 0;
        do {
            $current = $this->move($subject[$index], $nextNodes);
            $current[] = $this->nfa->startNode;
            $nextNodes = $this->closure($current);
            if (in_array($this->nfa->endNode, $nextNodes)) {
                return true;
            }
        } while (++$index < $len);

        return false;
    }

    /**
     * ε闭包运算
     *
     * @param array $inputNodes
     * @return array
     */
    public function closure(array $inputNodes):array
    {
        $outputNodes = [];
        foreach ($inputNodes as $inputNode) {
            $edge1 = $inputNode->getEdge1();
            if (!is_null($edge1) && $edge1->getType() == Edge::TYPE_EPSILON)
            {
                $outputNodes[] = $inputNode->getNext1();
            }
            $edge2 = $inputNode->getEdge2();
            if (!is_null($edge2) && $edge2->getType() == Edge::TYPE_EPSILON)
            {
                $outputNodes[] = $inputNode->getNext2();
            }
        }

        if (!empty($outputNodes)) {
            $outputNodes = array_merge($inputNodes, $this->closure($outputNodes));
        } else {
            $outputNodes = $inputNodes;
        }

        return $outputNodes;
    }

    /**
     * 状态转移
     *
     * @param string $char
     * @param array $inputNodes
     * @return array
     */
    public function move(string $char, array $inputNodes):array
    {
        $outputNodes = [];

        foreach ($inputNodes as $inputNode)
        {
            $edge1 = $inputNode->getEdge1();
            if (!is_null($edge1)) {
                switch ($edge1->getType()) {
                    case Edge::TYPE_CHARSET:
                        if (in_array($char, $edge1->getCharSet())) {
                            $outputNodes[] = $inputNode->getNext1();
                        }
                        break;
                    case Edge::TYPE_CHARSET_NEGATIVE:
                        if (!in_array($char, $edge1->getCharSet())) {
                            $outputNodes[] = $inputNode->getNext1();
                        }
                        break;
                    case Edge::TYPE_ANY:
                        $outputNodes[] = $inputNode->getNext1();
                        break;
                }
            }

            $edge2 = $inputNode->getEdge2();
            if (!is_null($edge2)) {
                switch ($edge2->getType()) {
                    case Edge::TYPE_CHARSET:
                        if (in_array($char, $edge2->getCharSet())) {
                            $outputNodes[] = $inputNode->getNext2();
                        }
                        break;
                    case Edge::TYPE_CHARSET_NEGATIVE:
                        if (!in_array($char, $edge2->getCharSet())) {
                            $outputNodes[] = $inputNode->getNext2();
                        }
                        break;
                    case Edge::TYPE_ANY:
                        $outputNodes[] = $inputNode->getNext2();
                        break;
                }
            }
        }

        return $outputNodes;
    }
}
