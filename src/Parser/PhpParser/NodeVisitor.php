<?php

declare(strict_types=1);

namespace Whisky\Parser\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    private array $inputVariables = [];
    private array $outputVaraibles = [];
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

    public function enterNode(Node $node)
    {
        if ($node instanceof Assign) {
            array_unshift($this->assignStack, $node);
            $this->assignLeftSide = $node->var;
            $this->assignRightSide = $node->expr;
        }
        if ($node instanceof Variable && is_string($node->name)) {
            if ($this->assignLeftSide && !in_array($node->name, $this->outputVaraibles, true) &&
                ($this->assignLeftSide === $node || $this->isChildOfNode($this->assignLeftSide, $node))) {
                $this->outputVaraibles[] = $node->name;
            } elseif ($this->assignRightSide && !in_array($node->name, $this->inputVariables, true) &&
                ($this->assignRightSide === $node || $this->isChildOfNode($this->assignRightSide, $node))) {
                $this->inputVariables[] = $node->name;
            }
        }

        return null;
    }

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

        return null;
    }
}
