<?php

declare(strict_types=1);

namespace Whisky\Parser;

class ParseResult
{
    private string $parsedCode;
    private array $inputVariables;
    private array $outputVariables;
    private array $functionCalls;

    public function __construct(string $parsedCode, array $inputVariables, array $outputVariables, array $functionCalls)
    {
        $this->parsedCode = $parsedCode;
        $this->inputVariables = $inputVariables;
        $this->outputVariables = $outputVariables;
        $this->functionCalls = $functionCalls;
    }

    public function getParsedCode(): string
    {
        return $this->parsedCode;
    }

    public function getInputVariables(): array
    {
        return $this->inputVariables;
    }

    public function getOutputVariables(): array
    {
        return $this->outputVariables;
    }

    public function getFunctionCalls(): array
    {
        return $this->functionCalls;
    }
}
