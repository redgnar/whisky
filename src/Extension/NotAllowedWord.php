<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\ParseError;

trait NotAllowedWord
{
    private function clearCodeFromStringsAndComments(string $code): string
    {
        // Remove strings
        $code = preg_replace("/([\"'])(?:\\\\.|[^\\\\])*?\\1/", '""', $code) ?: '';
        // Remove comments
        $code = preg_replace("~(?:#|//)[^\r\n]*|/\*.*?\*/~s", '', $code) ?: '';

        return $code;
    }

    private function isWordAllowed(string $word, string $codeWithoutStrings): void
    {
        if (1 === preg_match('/(^|\W)'.str_replace(['$'], ['\$'], $word).'($|\W)/', $codeWithoutStrings)) {
            throw new ParseError($this->getNotAllowedWordErrorMessage($word));
        }
    }

    private function getNotAllowedWordErrorMessage(string $word): string
    {
        return 'Using '.$word.' is not allowed';
    }

    /**
     * @param string[] $functions
     */
    private function getNotAllowedFunctionsErrorMessage(array $functions): string
    {
        return 'Using not allowed functions: '.implode(', ', $functions);
    }
}
