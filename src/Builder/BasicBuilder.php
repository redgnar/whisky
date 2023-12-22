<?php

declare(strict_types=1);

namespace Whisky\Builder;

use Whisky\Builder;
use Whisky\Extension;
use Whisky\Parser;
use Whisky\Scope;
use Whisky\Script;
use Whisky\Script\BasicScript;

class BasicBuilder implements Builder
{
    private Parser $parser;
    /**
     * @var Extension[]
     */
    private array $extensions = [];

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function build(string $code, Scope $functions = null): Script
    {
        $functions ??= new Scope\BasicScope();
        $parseResult = $this->parser->parse($code);
        $resultCode = $parseResult->getParsedCode();
        foreach ($this->extensions as $extension) {
            $resultCode = $extension->build($resultCode, $parseResult, $functions);
        }
        $resultCode .= ' return $variables["return"] ?? null;';

        return $this->createScript(
            $code,
            $resultCode,
            eval(sprintf(
                $this->getCodeRunnerTemplate(),
                $resultCode,
            ))
        );
    }

    public function addExtension(Extension $extension): void
    {
        $this->extensions[] = $extension;
    }

    protected function createScript(string $code, string $resultCode, \Closure $codeRunner): Script
    {
        return new BasicScript($code, $resultCode, $codeRunner);
    }

    protected function getCodeRunnerTemplate(): string
    {
        return <<<'EOD'
return function(\Whisky\Scope $variables) use($functions) : mixed {
%s
};
EOD;
    }
}
