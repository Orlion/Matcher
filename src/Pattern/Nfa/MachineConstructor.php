<?php

namespace Orlion\Matcher\Pattern\Nfa;


use Orlion\Matcher\Exception\MatcherException;
use Orlion\Matcher\Pattern\Lexer;

class MachineConstructor
{
    private $lexer;
    private $nodeFactory;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->nodeFactory = new NodeFactory();
    }

    /**
     * @return Nfa
     * @throws MatcherException
     */
    public function build(): Nfa
    {
        $nfa = new Nfa();
        $this->expr($nfa);
        return $nfa;
    }

    /**
     * @param Nfa $nfaOut
     * @throws MatcherException
     */
    private function expr(Nfa $nfaOut): void
    {
        $this->or($nfaOut);
    }

    /**
     * 为"|"构造
     *
     * @param Nfa $nfa
     * @return bool
     * @throws MatcherException
     */
    private function or(Nfa $nfaOut): void
    {
        $this->subExpr($nfaOut);
        while (!$this->lexer->isEnd()) {
            if ($this->lexer->getCurrentToken() != Lexer::TOKEN_OR) {
                throw new MatcherException('不能解析的token at offset:' . $this->lexer->getCurrentIndex(), 1);
            }

            $nfaLocal = new Nfa();
            if (!$this->subExpr($nfaLocal)) {

            }

            $start = $this->nodeFactory->getNode();
            $start->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
            $start->setNext1($nfaOut->startNode);
            $start->setEdge2(new Edge(Edge::TYPE_EPSILON, []));
            $start->setNext2($nfaLocal->startNode);

            $end = $this->nodeFactory->getNode();
            $nfaOut->endNode->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
            $nfaOut->endNode->setNext1($end);
            $nfaLocal->endNode->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
            $nfaLocal->endNode->setNext1($end);

            $nfaOut->startNode = $start;
            $nfaOut->endNode = $end;
        }
    }

    /**
     * subExpr -> factor*
     *
     * @param Nfa $nfaOut
     * @return Nfa
     * @throws MatcherException
     */
    private function subExpr(Nfa $nfaOut): bool
    {
        if ($this->lexer->advance()) {
            if (!$this->factor($nfaOut)) {
                return false;
            }
            while (!$this->lexer->isEnd())
            {
                $nfaLocal = new Nfa();
                if (!$this->factor($nfaLocal)) {
                    break;
                }

                $nfaOut->endNode->setNext1($nfaLocal->startNode);
                $nfaOut->endNode->setEdge1(new Edge(Edge::TYPE_EPSILON, []));

                $nfaOut->endNode = $nfaLocal->endNode;
            }

            return true;
        }

        return false;
    }

    /**
     * factor -> term | term* | term+ | term?
     *
     * @param Nfa $nfa
     * @return bool
     * @throws MatcherException
     */
    private function factor(Nfa $nfa): bool
    {
        $handle = $this->term($nfa);
        if (!$this->starClosure($nfa)) {
            if (!$this->plusClosure($nfa)) {
                $this->optionClosure($nfa);
            }
        }

        return $handle;
    }

    /**
     * term* 构造
     *
     * @param Nfa $nfa
     * @return bool
     */
    private function starClosure(Nfa $nfa): bool
    {
        if ($this->lexer->getCurrentToken() != Lexer::TOKEN_STAR) {
            return false;
        }

        $start = $this->nodeFactory->getNode();
        $start->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
        $start->setNext1($nfa->startNode);

        $end = $this->nodeFactory->getNode();

        $start->setEdge2(new Edge(Edge::TYPE_EPSILON, []));
        $start->setNext2($end);

        // TODO: 这里理论上不能确定是setEdge1还是setEdge2
        $nfa->endNode->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
        $nfa->endNode->setNext1($end);
        $nfa->endNode->setEdge2(new Edge(Edge::TYPE_EPSILON, []));
        $nfa->endNode->setNext2($nfa->startNode);

        $nfa->startNode = $start;
        $nfa->endNode = $end;

        $this->lexer->advance();

        return true;
    }

    /**
     * term+ 构造
     *
     * @param Nfa $nfa
     * @return bool
     */
    private function plusClosure(Nfa $nfa): bool
    {
        if ($this->lexer->getCurrentToken() != Lexer::TOKEN_PLUS) {
            return false;
        }

        $start = $this->nodeFactory->getNode();
        $start->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
        $start->setNext1($nfa->startNode);

        $end = $this->nodeFactory->getNode();

        // TODO: 这里理论上不能确定是setEdge1还是setEdge2
        $nfa->endNode->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
        $nfa->endNode->setNext1($end);
        $nfa->endNode->setEdge2(new Edge(Edge::TYPE_EPSILON, []));
        $nfa->endNode->setNext2($nfa->startNode);

        $nfa->startNode = $start;
        $nfa->endNode = $end;

        $this->lexer->advance();

        return true;
    }

    /**
     * term? 构造
     *
     * @param Nfa $nfa
     * @return bool
     */
    private function optionClosure(Nfa $nfa): bool
    {
        if ($this->lexer->getCurrentToken() != Lexer::TOKEN_OPTIONAL) {
            return false;
        }

        $start = $this->nodeFactory->getNode();
        $start->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
        $start->setNext1($nfa->startNode);

        $end = $this->nodeFactory->getNode();

        // TODO: 这里理论上不能确定是setEdge1还是setEdge2
        $nfa->endNode->setEdge1(new Edge(Edge::TYPE_EPSILON, []));
        $nfa->endNode->setNext1($end);

        $nfa->startNode = $start;
        $nfa->endNode = $end;

        $this->lexer->advance();

        return true;
    }

    /**
     * term构造
     * term -> char | [...] | [^...]
     *
     * @param Nfa $nfa
     * @return Nfa
     * @throws MatcherException
     */
    private function term(Nfa $nfa): bool
    {
        $handle = $this->char($nfa);
        if (!$handle) {
            $handle = $this->dot($nfa);
            if (!$handle) {
                $handle = $this->charSet($nfa);
            }
        }

        return $handle;
    }

    /**
     * 单字符构造
     *
     * @param Nfa $nfa
     * @return bool
     */
    private function char(Nfa $nfa): bool
    {
        if ($this->lexer->getCurrentToken() != Lexer::TOKEN_CHAR) {
            return false;
        }
        $nfa->startNode = $node = $this->nodeFactory->getNode();
        $node->setEdge1(new Edge(Edge::TYPE_CHARSET, [$this->lexer->getCurrentLexeme()]));

        $node = $this->nodeFactory->getNode();
        $nfa->endNode = $node;

        $nfa->startNode->setNext1($node);

        $this->lexer->advance();

        return true;
    }

    /**
     * 为"."构建nfa
     *
     * @param Nfa $nfa
     * @return bool
     */
    private function dot(Nfa $nfa): bool
    {
        if ($this->lexer->getCurrentToken() != Lexer::TOKEN_ANY) {
            return false;
        }
        $nfa->startNode = $node = $this->nodeFactory->getNode();
        $node->setEdge1(new Edge(Edge::TYPE_ANY, []));

        $node = $this->nodeFactory->getNode();
        $nfa->endNode = $node;

        $nfa->startNode->setNext1($node);

        $this->lexer->advance();

        return true;
    }

    /**
     * 为"[xxxxxx]"或"[^xxxxxx]"构建nfa
     *
     * @param Nfa $nfa
     * @return bool
     * @throws MatcherException
     */
    private function charSet(Nfa $nfa): bool
    {
        if ($this->lexer->getCurrentToken() != Lexer::TOKEN_CHARSET_BEGIN) {
            return false;
        }

        // 是否是取返
        $isNegative = false;

        // 步进取下一个字符
        $this->lexer->advance();
        if ($this->lexer->getCurrentToken() == Lexer::TOKEN_CARET) {
            $isNegative = true;
            $this->lexer->advance();
        }

        $charSet = [];
        while ($this->lexer->getCurrentToken() != Lexer::TOKEN_CHARSET_END) {
            $charSet[] = $this->lexer->getCurrentLexeme();
            if (!$this->lexer->advance()) {
                // [未闭合就结束则正则表达式不完整，抛出异常警告
                throw new MatcherException('正则表达式字符集未正确闭合 at offset:' . $this->lexer->getCurrentIndex(), 1);
            }
        }

        $nfa->startNode = $node = $this->nodeFactory->getNode();
        $node->setEdge1(new Edge($isNegative ? Edge::TYPE_CHARSET_NEGATIVE : Edge::TYPE_CHARSET, $charSet));

        $node = $this->nodeFactory->getNode();
        $nfa->endNode = $node;

        $nfa->startNode->setNext1($node);

        $this->lexer->advance();

        return true;
    }
}
