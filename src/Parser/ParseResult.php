<?php

declare(strict_types=1);

namespace Whisky\Parser;

class ParseResult
{
    private string $parsedCode;
    private array $inputVariables;
    private array $outputVariables;

    public function __construct(string $parsedCode, array $inputVariables, array $outputVariables)
    {
        $this->parsedCode = $parsedCode;
        $this->inputVariables = $inputVariables;
        $this->outputVariables = $outputVariables;
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
}
