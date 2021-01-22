<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\ParseError;
use Whisky\Parser\ParseResult;

class BasicSecurity implements Extension
{
    public function parse(string $code): void
    {
        $notAllowedWords = ['$this', 'for', 'while', 'do', 'function', 'class', 'new'];
        foreach ($notAllowedWords as $notAllowedWord) {

            if (1 === preg_match('/'.str_replace(['$'], ['\$'], $notAllowedWord).'([\W])/', $code)) {
                throw new ParseError('Using '.$notAllowedWord.' is not allowed');
            }
        }

    }

    public function normalize(string $code): string
    {
        return $code;
    }

    public function secure(ParseResult $parseResult): void
    {
        $allowedFunctions = ['substr'];
        foreach ($parseResult->getFunctionCalls() as $functionName) {
            if (!in_array($functionName, $allowedFunctions, true)) {
                throw new ParseError('Calling '.$functionName.' function is not allowed');
            }
        }
    }
}
