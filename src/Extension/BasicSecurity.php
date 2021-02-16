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
        $notAllowedWords = ['$this', 'for', 'while', 'do', 'function', 'class', 'new', 'namespace', 'use', 'define', 'const'];
        foreach ($notAllowedWords as $notAllowedWord) {
            if (1 === preg_match('/(^|\W)'.str_replace(['$'], ['\$'], $notAllowedWord).'($|\W)/', $code)) {
                throw new ParseError('Using '.$notAllowedWord.' is not allowed');
            }
        }

        return $code;
    }

    public function secure(ParseResult $parseResult): void
    {
    }
}
