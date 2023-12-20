<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\ParseError;
use Whisky\Parser\ParseResult;
use Whisky\Scope;

class BasicSecurity implements Extension
{
    private const NOT_ALLOWED_WORDS = [
        '$environment', '$scope', '$this', 'die', 'exit', 'for', 'while', 'do',
        'function', 'class', 'trait', 'abstract', 'include', 'include_once',
        'require', 'require_once', 'interface', 'public', 'private', 'protected',
        'new', 'namespace', 'use', 'define', 'const', 'declare', 'enddeclare',
        'static', 'echo', 'eval', 'print', 'printf', 'printr', 'var_dump',
        'var_export', '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__',
        '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__',
    ];

    private const NOT_ALLOWED_FUNCTIONS = [
        'file_exists', 'file_put_contents', 'file_get_contents', 'readfile',
        'readlink', 'readdir', 'is_writable', 'is_readable',
    ];

    public function build(string $code, ParseResult $parseResult, Scope $environment): string
    {
        $codeWithoutStrings = preg_replace("/([\"'])(?:\\\\.|[^\\\\])*?\\1/", '""', $code) ?: '';
        foreach (self::NOT_ALLOWED_WORDS as $notAllowedWord) {
            $this->isWordAllowed($notAllowedWord, $codeWithoutStrings);
        }

        $notAllowedFunctionsUsed = array_intersect(self::NOT_ALLOWED_FUNCTIONS, $parseResult->getFunctionCalls());
        if (!empty($notAllowedFunctionsUsed)) {
            throw new ParseError($this->getNotAllowedFunctionsErrorMessage($notAllowedFunctionsUsed));
        }

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
