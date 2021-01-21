<?php

namespace Whisky\Parser;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser as NikicPhpParser;
use PhpParser\PrettyPrinter;
use Whisky\Parser;

class PhpParser implements Parser
{
    protected NikicPhpParser $nikicPhpParser;

    public function __construct(NikicPhpParser $nikicPhpParser)
    {
        $this->nikicPhpParser = $nikicPhpParser;
    }

    public function parse(string $code): string
    {
        /** @var array<Node> $ast */
        $ast = $this->nikicPhpParser->parse($this->prepareForParse($code));
        $traverser = new NodeTraverser();
        $visitor = new class() extends NodeVisitorAbstract {
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
        };
        $traverser->addVisitor($visitor);
        $ast = $traverser->traverse($ast);

        $prettyPrinter = new PrettyPrinter\Standard();

        return $this->prepareForOutput(
            $prettyPrinter->prettyPrintFile($ast),
            $visitor->getInputVariables(),
            $visitor->getOutputVaraibles()
        );
    }

    protected function prepareForParse(string $code): string
    {
        if (false === strpos($code, '<?php ')) {
            $code = '<?php '.$code;
        }

        if (1 === preg_match('/\$this([\W])/', $code)) {
            throw new \Exception('Using $this is not allowed');
        }
//        // Replace all variables with call $this->{$var}
//        $code = preg_replace('/\$(\w+)/', '$this["${1}"]', $code);

        return $code;
    }

    protected function prepareForOutput(string $code, array $inputVariables, array $outputVariables): string
    {
        $resultCode = trim(str_replace('<?php', '', $code));
        $preCode = '';
        if (!empty($inputVariables)) {
            foreach ($inputVariables as $variable) {
                $preCode .= '$'.$variable.'=$this[\''.$variable.'\'] ?? null;';
            }
            $resultCode = $preCode."\n".$resultCode;
        }
        $postCode = '';
        if (!empty($outputVariables)) {
            foreach ($outputVariables as $variable) {
                $postCode .= '$this[\''.$variable.'\']=$'.$variable.';';
            }
            $resultCode .= "\n".$postCode;
        }

        return $resultCode;
    }
}
