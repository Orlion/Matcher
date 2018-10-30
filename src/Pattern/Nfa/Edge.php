<?php

namespace Orlion\Matcher\Pattern\Nfa;

/**
 * Nfa Edge
 * @package Orlion\Pattern\Nfa
 */
class Edge
{
    // 边是ε
    const TYPE_EPSILON = 0;
    // 边是字符集
    const TYPE_CHARSET = 1;
    // 边是字符集取反
    const TYPE_CHARSET_NEGATIVE = 2;
    // 边是任意字符
    const TYPE_ANY = 3;

    private $type;

    private $charSet;

    public function __construct(int $type, array $charSet)
    {
        $this->type = $type;
        $this->charSet = $charSet;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getCharSet(): array
    {
        return $this->charSet;
    }
}
