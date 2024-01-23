<?php

declare(strict_types=1);

namespace Whisky\Parser\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;
use Whisky\ParseError;

class FunctionVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $functionCalls = [];

    /**
     * @return string[]
     */
    public function getFunctionCalls(): array
    {
        return $this->functionCalls;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof FuncCall) {
            // FUNCTION CALLS
            if ($node->name instanceof Name
                && is_string($node->name->getParts()[0])
                && !in_array($node->name->getParts()[0], $this->functionCalls, true)) {
                $this->functionCalls[] = $node->name->getParts()[0];
            }
        } elseif ($node instanceof Node\Stmt\Function_) {
            throw new ParseError('Declaration of function is not allowed');
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        return null;
    }
}
