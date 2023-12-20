<?php

declare(strict_types=1);

namespace Whisky\Parser;

class ParseResult
{
    /**
     * @param string[] $inputVariables
     * @param string[] $outputVariables
     * @param string[] $functionCalls
     */
    public function __construct(private readonly string $parsedCode, private readonly array $inputVariables, private readonly array $outputVariables, private readonly array $functionCalls)
    {
    }

    public function getParsedCode(): string
    {
        return $this->parsedCode;
    }

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
    public function getOutputVariables(): array
    {
        return $this->outputVariables;
    }

    /**
     * @return string[]
     */
    public function getFunctionCalls(): array
    {
        return $this->functionCalls;
    }
}
