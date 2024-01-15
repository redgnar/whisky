<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\Function\FunctionRepository;
use Whisky\Parser\ParseResult;

class FunctionHandler implements Extension
{
    use NotAllowedWord;

    private const NOT_ALLOWED_WORDS = [
        '$functions',
    ];

    public function __construct(
        private readonly FunctionRepository $functionRepository
    ) {
    }

    public function parse(string $code): string
    {
        $codeWithoutStrings = $this->clearCodeFromStrings($code);
        foreach (self::NOT_ALLOWED_WORDS as $notAllowedWord) {
            $this->isWordAllowed($notAllowedWord, $codeWithoutStrings);
        }

        return $code;
    }

    public function build(string $code, ParseResult $parseResult): string
    {
        foreach ($parseResult->getFunctionCalls() as $functionName) {
            if (!function_exists($functionName)) {
                if ($this->functionRepository->has($functionName)) {
                    $code = preg_replace('/(^|\W)'.str_replace(['$'], ['\$'], $functionName).'($|\W)/', '${1}$functions[\''.$functionName.'\']${2}', $code) ?? '';
                }
            }
        }

        return $code;
    }
}
