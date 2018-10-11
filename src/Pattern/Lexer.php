<?php

namespace Orlion\Pattern;


class Lexer
{
    const TOKEN_MAP = [
        'CHAR' =>  1, // 字符
    ];

    private $input;
    private $inputLen;
    private $index = -1;
    private $currentToken;
    private $currentLexeme;

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->inputLen = strlen($input);
    }

    /**
     * 步进
     *
     * @return bool
     */
    public function advance():bool
    {
        $this->index++;
        if ($this->index >= $this->inputLen)
        {
            return false;
        }

        $this->currentLexeme = $this->input[$this->index];

        return true;
    }

    public function getCurrentToken():int
    {
        return $this->currentToken;
    }

    public function getLexeme()
    {
        return $this->currentLexeme;
    }
}
