<?php

namespace Orlion\Pattern\Nfa;


class Node
{
    // 节点没有出去的边
    const EMPTY = 0;
    // 节点出去的边是ε
    const EPSILON = 1;
    // 节点出去的边是字符集
    const CHAR_SET = 2;

    // 状态
    private $state;

    // 下一个节点
    private $next;
    // 下一个节点2
    private $next2;

    // 边
    private $edge;

    private $charSet;

    public function __construct(int $state)
    {
        $this->state = $state;
    }

    /**
     * @return string | int
     */
    public function getEdge()
    {
        return $this->edge;
    }

    /**
     * @param string | int $edgeType
     */
    public function setEdge($edge)
    {
        $this->edge = $edge;
    }

    /**
     * @return array
     */
    public function getCharSet():array
    {
        return $this->charSet;
    }

    /**
     * @param array $charSet
     */
    public function setCharSet(array $charSet)
    {
        $this->charSet = $charSet;
    }

    /**
     * @return Node
     */
    public function getNext():Node
    {
        return $this->next;
    }

    /**
     * @param Node $next
     */
    public function setNext(Node $next)
    {
        $this->next = $next;
    }

    /**
     * @return Node
     */
    public function getNext2():Node
    {
        return $this->next2;
    }

    /**
     * @param Node $next2
     */
    public function setNext2(Node $next2)
    {
        $this->next2 = $next2;
    }
}