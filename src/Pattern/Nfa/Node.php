<?php

namespace Orlion\Matcher\Pattern\Nfa;


class Node
{
    // 状态
    private $state;
    // 下一个节点
    private $next1;
    // 下一个节点2
    private $next2;
    // 边
    private $edge1;
    // 边2
    private $edge2;

    public function __construct(int $state)
    {
        $this->state = $state;
    }

    /**
     * @return Edge
     */
    public function getEdge1()
    {
        return $this->edge1;
    }

    /**
     * @param Edge $edge
     */
    public function setEdge1(Edge $edge)
    {
        $this->edge1 = $edge;
    }

    /**
     * @return Edge
     */
    public function getEdge2()
    {
        return $this->edge2;
    }

    /**
     * @param Edge $edge
     */
    public function setEdge2(Edge $edge)
    {
        $this->edge2 = $edge;
    }

    /**
     * @return Node
     */
    public function getNext1(): Node
    {
        return $this->next1;
    }

    /**
     * @param Node $next
     */
    public function setNext1(Node $next)
    {
        $this->next1 = $next;
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