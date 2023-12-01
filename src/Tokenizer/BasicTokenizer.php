<?php

declare(strict_types=1);

namespace Whisky\Tokenizer;

class BasicTokenizer implements \Whisky\Tokenizer
{
    protected const SPECIAL_WORDS = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        '__CLASS__',
        '__DIR__',
        '__FILE__',
        '__FUNCTION__',
        '__LINE__',
        '__METHOD__',
        '__NAMESPACE__',
        '__TRAIT__',
    ];

    public function tokenize(string $code): TokenizedCode
    {
        $tokens = [];
        $tokenIndex = 0;
        $codeLength = mb_strlen($code);
        if (0 === $codeLength) {
            return new TokenizedCode($code, $tokens);
        }
        $codePosition = 0;
        $currentCharacter = $code[$codePosition];
        $previousCharacters = '';
        $mode = $this->determineMode($currentCharacter);
        while (++$codePosition <= $codeLength) {
            $previousCharacters .= $currentCharacter;
            if ($codePosition < $codeLength) {
                $currentCharacter = $code[$codePosition];
                $newMode = $this->determineMode($currentCharacter);
            } else {
                $currentCharacter = '';
                $newMode = 'SKIP';
            }
            switch ($mode) {
                case 'WORD':
                    if ('WORD' !== $newMode) {
                        $tokens[] = new Token(
                            $previousCharacters,
                            $this->isSpecialWord($previousCharacters) ? Token::SPECIAL_WORD : Token::WORD,
                            $tokenIndex++
                        );
                        $previousCharacters = '';
                    }
                    break;
                case 'SPACE':
                    if ('SPACE' !== $newMode) {
                        $tokens[] = new Token($previousCharacters, Token::SPACE, $tokenIndex++);
                        $previousCharacters = '';
                    }
                    break;
                case 'OTHER':
                    $tokens[] = new Token($previousCharacters, Token::OTHER, $tokenIndex++);
                    $previousCharacters = '';
                    break;
                case 'S_STRING':
                    if ('S_STRING' === $newMode && '\\' !== substr($previousCharacters, -1)) {
                        $newMode = 'SKIP';
                        $tokens[] = new Token($previousCharacters.$currentCharacter, Token::STRING, $tokenIndex++);
                    } else {
                        $newMode = 'S_STRING';
                    }
                    break;
                case 'D_STRING':
                    if ('D_STRING' === $newMode && '\\' !== substr($previousCharacters, -1)) {
                        $newMode = 'SKIP';
                        $tokens[] = new Token($previousCharacters.$currentCharacter, Token::STRING, $tokenIndex++);
                    } else {
                        $newMode = 'D_STRING';
                    }
                    break;
                case 'SKIP':
                    $previousCharacters = '';
                    break;
            }
            $mode = $newMode;
        }

        return new TokenizedCode($code, $tokens);
    }

    protected function determineMode(string $character): string
    {
        if (1 === preg_match('/[\w_]/', $character)) {
            return 'WORD';
        }
        if (1 === preg_match('/\s/', $character)) {
            return 'SPACE';
        }
        if (1 === preg_match('/\'/', $character)) {
            return 'S_STRING';
        }
        if (1 === preg_match('/"/', $character)) {
            return 'D_STRING';
        }

        return 'OTHER';
    }

    protected function isSpecialWord(string $word): bool
    {
        return in_array($word, self::SPECIAL_WORDS, true);
    }
}
