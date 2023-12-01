<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\ParseError;
use Whisky\Parser\ParseResult;

class BasicSecurity implements Extension
{
    public function transformCode(string $code): string
    {
        /** @var string $codeWithoutStrings */
        $codeWithoutStrings = preg_replace("/([\"'])(?:\\\\.|[^\\\\])*?\\1/", '""', $code);
        $notAllowedWords = [
            '$this',
            'die',
            'exit',
            // loops without foreach
            'for',
            'while',
            'do',
            // definitions
            'function',
            'class',
            'trait',
            'abstract',
            'include',
            'include_once',
            'require',
            'require_once',
            'interface',
            'public',
            'private',
            'protected',
            'new',
            'namespace',
            'use',
            'define',
            'const',
            'declare',
            'enddeclare',
            'return',
            'static',
            // functions
            'echo',
            'eval',
            'print',
            'printf',
            'printr',
            'var_dump',
            'var_export',
            // Constants
            '__CLASS__',
            '__DIR__',
            '__FILE__',
            '__FUNCTION__',
            '__LINE__',
            '__METHOD__',
            '__NAMESPACE__',
            '__TRAIT__',
        ];
        foreach ($notAllowedWords as $notAllowedWord) {
            if (1 === preg_match(
                '/(^|\W)'.str_replace(['$'], ['\$'], $notAllowedWord).'($|\W)/',
                $codeWithoutStrings
            )) {
                throw new ParseError('Using '.$notAllowedWord.' is not allowed');
            }
        }

        return $code;
    }

    public function secure(ParseResult $parseResult): void
    {
    }
}
