<?php

declare(strict_types=1);

namespace Whisky\Builder;

use Whisky\Builder;
use Whisky\Extension;
use Whisky\Parser;
use Whisky\Parser\ParseResult;
use Whisky\Script;
use Whisky\Script\BasicScript;

class BasicBuilder implements Builder
{
    private Parser $parser;
    private Extension\VariableHandler $variableHandler;
    private Extension\FunctionHandler $functionHandler;

    /**
     * @var Extension[]
     */
    private array $extensions = [];

    public function __construct(
        Parser $parser,
        Extension\VariableHandler $variableHandler,
        Extension\FunctionHandler $functionHandler
    ) {
        $this->parser = $parser;
        $this->variableHandler = $variableHandler;
        $this->functionHandler = $functionHandler;

        $this->addExtension($this->variableHandler);
        $this->addExtension($this->functionHandler);
    }

    public function build(string $code): Script
    {
        $resultCode = $code;
        foreach ($this->extensions as $extension) {
            $resultCode = $extension->parse($resultCode);
        }
        $parseResult = $this->parser->parse($resultCode);
        $resultCode = $parseResult->getParsedCode();
        foreach ($this->extensions as $extension) {
            $resultCode = $extension->build($resultCode, $parseResult);
        }

        return $this->createScript(
            $code,
            $resultCode,
            $parseResult
        );
    }

    public function addExtension(Extension $extension): void
    {
        $this->extensions[] = $extension;
    }

    protected function createScript(string $code, string $resultCode, ParseResult $parseResult): Script
    {
        return new BasicScript($code, $resultCode, $parseResult);
    }
}
