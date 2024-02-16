<?php

declare(strict_types=1);

namespace Whisky\Extension;

use Whisky\Extension;
use Whisky\ParseError;
use Whisky\Parser\ParseResult;

class BasicSecurity implements Extension
{
    use NotAllowedWord;

    private const NOT_ALLOWED_WORDS = [
        '$this', 'die', 'exit', 'do',
        'class', 'trait', 'abstract', 'include', 'include_once',
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
        $notAllowedFunctionsUsed = array_intersect(self::NOT_ALLOWED_FUNCTIONS, $parseResult->getFunctionCalls());
        if (!empty($notAllowedFunctionsUsed)) {
            throw new ParseError($this->getNotAllowedFunctionsErrorMessage($notAllowedFunctionsUsed));
        }

        return $code;
    }
}
