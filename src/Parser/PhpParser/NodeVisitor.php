<?php

declare(strict_types=1);

namespace Whisky\Parser\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    private array $inputVariables = [];
    private array $outputVaraibles = [];
    private array $loopVariables = [];
    private array $functionCalls = [];
    private array $assignStack = [];
    private ?Node $assignLeftSide = null;
    private ?Node $assignRightSide = null;

    public function getInputVariables(): array
    {
        return $this->inputVariables;
    }

    public function getOutputVaraibles(): array
    {
        return $this->outputVaraibles;
    }

    public function getFunctionCalls(): array
    {
        return $this->functionCalls;
    }

    public function isChildOfNode(Node $node, Node $child): bool
    {
        foreach ($node->getSubNodeNames() as $name) {
            if ($node->{$name} instanceof Node &&
                ($node->{$name} === $child || $this->isChildOfNode($node->{$name}, $child))) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Assign) {
            array_unshift($this->assignStack, $node);
            $this->assignLeftSide = $node->var;
            $this->assignRightSide = $node->expr;
        }
        if ($node instanceof Node\Stmt\Foreach_) {
            if ($node->keyVar instanceof Variable && is_string($node->keyVar->name)) {
                array_unshift($this->loopVariables, $node->keyVar->name);
            }
            if ($node->valueVar instanceof Variable && is_string($node->valueVar->name)) {
                array_unshift($this->loopVariables, $node->valueVar->name);
            }
        }
        if ($node instanceof Variable && is_string($node->name) && !in_array(
                $node->name,
                $this->outputVaraibles,
                true
            ) && !in_array(
                $node->name,
                $this->loopVariables,
                true
            )) {
            if ($this->assignLeftSide &&
                ($this->assignLeftSide === $node || $this->isChildOfNode($this->assignLeftSide, $node))) {
                $this->outputVaraibles[] = $node->name;
            } elseif ($this->assignRightSide &&
                !in_array($node->name, $this->inputVariables, true) &&
                ($this->assignRightSide === $node || $this->isChildOfNode($this->assignRightSide, $node))) {
                $this->inputVariables[] = $node->name;
            }
        }
        if ($node instanceof FuncCall &&
            $node->name instanceof Name &&
            is_string($node->name->parts[0]) &&
            !in_array($node->name->parts[0], $this->functionCalls, true)
        ) {
            $this->functionCalls[] = $node->name->parts[0];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Assign) {
            if (!empty($this->assignStack) && $this->assignStack[0] === $node) {
                array_shift($this->assignStack);
                if (!empty($this->assignStack)) {
                    /** @var Assign $assignNode */
                    $assignNode = $this->assignStack[0];
                    $this->assignLeftSide = $assignNode->var;
                    $this->assignRightSide = $assignNode->expr;
                } else {
                    $this->assignLeftSide = null;
                    $this->assignRightSide = null;
                }
            }
        }
        if ($node instanceof Node\Stmt\Foreach_ && !empty($this->loopVariables)) {
            if ($node->valueVar instanceof Variable && is_string(
                    $node->valueVar->name
                ) && $node->valueVar->name === $this->loopVariables[0]) {
                array_shift($this->loopVariables);
            }
            if ($node->keyVar instanceof Variable && is_string(
                    $node->keyVar->name
                ) && $node->keyVar->name === $this->loopVariables[0]) {
                array_shift($this->loopVariables);
            }
        }

        return null;
    }
}
