<?php

declare(strict_types=1);

namespace Whisky\Parser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser as NikicPhpParser;
use PhpParser\PrettyPrinter;
use Whisky\ParseError;
use Whisky\Parser;
use Whisky\Parser\PhpParser\NodeVisitor;

class PhpParser implements Parser
{
    protected NikicPhpParser $nikicPhpParser;

    public function __construct(NikicPhpParser $nikicPhpParser)
    {
        $this->nikicPhpParser = $nikicPhpParser;
    }

    public function parse(string $code): ParseResult
    {
        try {
            /** @var array<Node> $ast */
            $ast = $this->nikicPhpParser->parse($this->processCodeBeforeParse($code));
        } catch (\PhpParser\Error $e) {
            throw new ParseError($e->getMessage());
        }
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor();
        $traverser->addVisitor($visitor);
        $ast = $traverser->traverse($ast);

        $prettyPrinter = new PrettyPrinter\Standard();

        return new ParseResult(
            $this->processCodeAfterParse($prettyPrinter->prettyPrintFile($ast)),
            $visitor->getInputVariables(),
            $visitor->getOutputVaraibles(),
            $visitor->getFunctionCalls()
        );
    }

    protected function processCodeBeforeParse(string $code): string
    {
        if (false === strpos($code, '<?php ')) {
            $code = '<?php '.$code;
        }

        return $code;
    }

    protected function processCodeAfterParse(string $code): string
    {
        return trim(str_replace('<?php', '', $code));
    }
}
