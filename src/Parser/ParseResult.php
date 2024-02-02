<?php

declare(strict_types=1);

namespace Whisky\Parser;

class ParseResult
{
    private string $parsedCode;
    /**
     * @var string[]
     */
    private array $inputVariables;
    /**
     * @var string[]
     */
    private array $outputVariables;
    /**
     * @var string[]
     */
    private array $functionCalls;
    private bool $returnValue;

    /**
     * @param string[] $inputVariables
     * @param string[] $outputVariables
     * @param string[] $functionCalls
     */
    public function __construct(
        string $parsedCode,
        array $inputVariables,
        array $outputVariables,
        array $functionCalls,
        bool $returnValue
    ) {
        $this->parsedCode = $parsedCode;
        $this->inputVariables = $inputVariables;
        $this->outputVariables = $outputVariables;
        $this->functionCalls = $functionCalls;
        $this->returnValue = $returnValue;
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

    public function hasReturnValue(): bool
    {
        return $this->returnValue;
    }
}
