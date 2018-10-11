<?php

namespace Orlion\Pattern\Nfa;


class Interpreter
{
    private $nfa;

    public function __construct(Nfa $nfa)
    {
        $this->nfa = $nfa;
    }

    public function interpret(string $subject):bool
    {
        // 下一个状态集
        $nextNodes = [];
        $nextNodes[] = $this->nfa->start;

        // ε闭包运算
        $nextNodes = $this->closure($nextNodes);

        $lastAccepted = false;

        $subjectLen = strlen($subject);
        for ($i = 0; $i < $subjectLen; $i++)
        {
            $nextNodes = $this->move($subject[$i], $nextNodes);
            $nextNodes = $this->closure($nextNodes);

            // 进入了接收状态则说明匹配
            if (in_array($this->nfa->end, $nextNodes))
            {
                $lastAccepted = true;
            }
        }

        return $lastAccepted;
    }

    /**
     * ε闭包运算
     *
     * @param array $inputNodes 输入状态
     * @return array 输出状态
     */
    public function closure(array $inputNodes):array
    {
        $outputNodes = $inputNodes;
        foreach ($inputNodes as $inputNode)
        {
            if (Node::EPSILON === $inputNode->getEdge())
            {
                $nextNode = $inputNode->getNext();
                if (null !== $nextNode)
                {
                    $outputNodes[] = $nextNode;
                }
                $next2Node = $inputNode->getNext2();
                if (null != $next2Node)
                {
                    $outputNodes[] = $next2Node;
                }
            }
        }

        return $outputNodes;
    }

    /**
     * 状态转移
     *
     * @param string $char      输入的字符
     * @param array $inputNodes 输入状态
     * @return array
     */
    public function move(string $char, array $inputNodes):array
    {
        $outputNodes = [];

        foreach ($inputNodes as $inputNode)
        {
            if (null != $inputNode->getNext() && ($char === $inputNode->getEdge() || ($inputNode->getEdge() === Node::CHAR_SET && in_array($char, $inputNode->getCharSet())))) {
                $outputNodes[] = $inputNode->getNext;
            }
        }

        return $outputNodes;
    }
}