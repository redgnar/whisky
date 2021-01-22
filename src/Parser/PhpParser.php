<?php

declare(strict_types=1);

namespace Whisky\Parser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use Whisky\ParseError;
use Whisky\Parser\PhpParser\NodeVisitor;
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

        return $this->processCodeAfterParse(
            $prettyPrinter->prettyPrintFile($ast),
            $visitor->getInputVariables(),
            $visitor->getOutputVaraibles()
        );
    }

    protected function processCodeBeforeParse(string $code): string
    {
        if (false === strpos($code, '<?php ')) {
            $code = '<?php '.$code;
        }

        return $code;
    }

    protected function processCodeAfterParse(string $code, array $inputVariables, array $outputVariables): string
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
