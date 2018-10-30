<?php

namespace Orlion\Matcher\Pattern;


class Lexer
{
    const TOKEN_CHAR = 1; // 常规字符
    const TOKEN_ANY = 2; // . (匹配任意字符)
    const TOKEN_CHARSET_BEGIN = 3; // [ (匹配字符集即[xxx]开始[)
    const TOKEN_CHARSET_END = 4; // ] (匹配字符集即[xxx]结束])
    const TOKEN_CARET = 5; // ^ (取反字符)
    const TOKEN_STAR = 6; // * (匹配前面的子表达式零次或多次)
    const TOKEN_PLUS = 7; // + (匹配前面的子表达式一次或多次)
    const TOKEN_OPTIONAL = 8; // ? (匹配前面的子表达式零次或一次)

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
        switch ($this->currentLexeme) {
            case '[':
                $this->currentToken = self::TOKEN_CHARSET_BEGIN;
                break;
            case ']':
                $this->currentToken = self::TOKEN_CHARSET_END;
                break;
            case '.':
                $this->currentToken = self::TOKEN_ANY;
                break;
            case '^':
                $this->currentToken = self::TOKEN_CARET;
                break;
            default:
                $this->currentToken = self::TOKEN_CHAR;
        }

        return true;
    }

    public function getCurrentToken(): int
    {
        return $this->currentToken;
    }

    public function getCurrentLexeme()
    {
        return $this->currentLexeme;
    }

    public function getCurrentIndex()
    {
        return $this->index;
    }
}
