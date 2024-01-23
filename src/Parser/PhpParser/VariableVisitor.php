<?php

declare(strict_types=1);

namespace Whisky\Parser\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;
use Whisky\ParseError;

class VariableVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private array $inputVariables = [];
    /**
     * @var string[]
     */
    private array $outputVaraibles = [];
    /**
     * @var string[]
     */
    private array $loopVariables = [];
    private bool $returnValue = false;
    /**
     * @var Node[]
     */
    private array $assignStack = [];
    private ?Node $assignLeftSide = null;
    private ?Node $assignRightSide = null;
    private bool $isInClosure = false;

    /**
     * @return string[]
     */
    public function getInputVariables(): array
    {
        return $this->inputVariables;
    }

    /**
     * @return string[]
     */
    public function getOutputVaraibles(): array
    {
        return $this->outputVaraibles;
    }

    public function hasReturnValue(): bool
    {
        return $this->returnValue;
    }

    public function hasReturnValue(): bool
    {
        return $this->returnValue;
    }

    public function isChildOfNode(Node $node, Node $child): bool
    {
        foreach ($node->getSubNodeNames() as $name) {
            if ($node->{$name} instanceof Node) {
                if ($node->{$name} === $child || $this->isChildOfNode($node->{$name}, $child)) {
                    return true;
                }
            } elseif (is_array($node->{$name})) {
                foreach ($node->{$name} as $subNode) {
                    if ($subNode instanceof Node) {
                        if ($subNode === $child || $this->isChildOfNode($subNode, $child)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function enterNode(Node $node)
    {
        if ($this->isInClosure) {
            return null;
        }

        if ($node instanceof Assign) {
            array_unshift($this->assignStack, $node);
            $this->assignLeftSide = $node->var;
            $this->assignRightSide = $node->expr;
        } elseif ($node instanceof Node\Stmt\Return_) {
            array_unshift($this->assignStack, $node);
            $this->assignRightSide = $node->expr;
        } elseif ($node instanceof Node\Stmt\Foreach_) {
            if ($node->keyVar instanceof Variable && is_string($node->keyVar->name)) {
                array_unshift($this->loopVariables, $node->keyVar->name);
            }
            if ($node->valueVar instanceof Variable && is_string($node->valueVar->name)) {
                array_unshift($this->loopVariables, $node->valueVar->name);
            }
        } elseif ($node instanceof Arg && $node->value instanceof Variable && is_string($node->value->name)
            && !in_array($node->value->name, $this->loopVariables, true)) {
            if (!in_array($node->value->name, $this->inputVariables, true)) {
                $this->inputVariables[] = $node->value->name;
            }
            // Function can modify variable, so it should be added to output vars
            if (!in_array($node->value->name, $this->outputVaraibles, true)) {
                $this->outputVaraibles[] = $node->value->name;
            }
        } elseif ($node instanceof Variable && is_string($node->name) && !in_array(
            $node->name,
            $this->outputVaraibles,
            true
        ) && !in_array(
            $node->name,
            $this->loopVariables,
            true
        )) {
            if ($this->assignRightSide) {
                // OUTPUT VARIABLES
                if ($this->assignLeftSide
                    && ($this->assignLeftSide === $node || $this->isChildOfNode($this->assignLeftSide, $node))) {
                    $this->outputVaraibles[] = $node->name;
                } elseif (!in_array($node->name, $this->inputVariables, true)
                    && ($this->assignRightSide === $node || $this->isChildOfNode($this->assignRightSide, $node))) {
                    $this->inputVariables[] = $node->name;
                }
            } else {
                // INPUT VARIABLES
                if (!in_array($node->name, $this->inputVariables, true)) {
                    $this->inputVariables[] = $node->name;
                }
            }
        } elseif ($node instanceof Node\Expr\Closure) {
            $this->isInClosure = true;
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Closure) {
            $this->isInClosure = false;

            return null;
        }
        if ($this->isInClosure) {
            return null;
        }
        if ($node instanceof Assign) {
            if (!empty($this->assignStack) && $this->assignStack[0] === $node) {
                array_shift($this->assignStack);
                if (!empty($this->assignStack)) {
                    /** @var Assign|Node\Stmt\Return_ $assignNode */
                    $assignNode = $this->assignStack[0];
                    $this->assignLeftSide = $assignNode->var ?? null;
                    $this->assignRightSide = $assignNode->expr;
                } else {
                    $this->assignLeftSide = null;
                    $this->assignRightSide = null;
                }
            }
        } elseif ($node instanceof Node\Stmt\Return_) {
            if (!empty($this->assignStack) && $this->assignStack[0] === $node) {
                array_shift($this->assignStack);
                if (!empty($this->assignStack)) {
                    /** @var Assign|Node\Stmt\Return_ $assignNode */
                    $assignNode = $this->assignStack[0];
                    $this->assignLeftSide = $assignNode->var ?? null;
                    $this->assignRightSide = $assignNode->expr;
                } else {
                    $this->assignLeftSide = null;
                    $this->assignRightSide = null;
                }
            }
            $this->returnValue = true;

            return new \PhpParser\Node\Stmt\Expression(new Assign(new Variable('return'), $node->expr ?? new Node\Expr\ConstFetch(new Name('null'))));
        } elseif ($node instanceof Node\Stmt\Foreach_ && !empty($this->loopVariables)) {
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
