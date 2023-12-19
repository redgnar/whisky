<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\Parser\ParseResult;
use Whisky\Scope;

class FunctionProvider implements Extension
{
    /**
     * @var array<string,\Closure>
     */
    private array $functionRegistry = [];

    public function build(string $code, ParseResult $parseResult, Scope $environment): string
    {
        foreach ($parseResult->getFunctionCalls() as $functionName) {
            if (!function_exists($functionName)) {
                if ($this->hasFunction($functionName)) {
                    $environment->set($functionName, $this->getFunction($functionName));
                    $code = preg_replace('/(^|\W)'.str_replace(['$'], ['\$'], $functionName).'($|\W)/', '${1}$environment[\''.$functionName.'\']${2}', $code) ?? '';
                }
            }
        }

        return $code;
    }

    public function addFunction(string $functionName, \Closure $closure): void
    {
        $this->functionRegistry[$functionName] = $closure;
    }

    public function hasFunction(string $functionName): bool
    {
        return array_key_exists($functionName, $this->functionRegistry);
    }

    public function getFunction(string $functionName): \Closure
    {
        if (!array_key_exists($functionName, $this->functionRegistry)) {
            throw new \InvalidArgumentException(sprintf('Function $1%s not declared', $functionName));
        }

        return $this->functionRegistry[$functionName];
    }
}
