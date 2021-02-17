<?php

declare(strict_types=1);

namespace Whisky\Tokenizer;

class TokenizedCode
{
    private string $code;
    /**
     * @var Token[]
     */
    private array $tokens;

    /**
     * @param Token[] $tokens
     */
    public function __construct(string $code, array $tokens)
    {
        $this->code = $code;
        $this->tokens = $tokens;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return Token[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param Token[] $tokens
     */
    public function setTokens(array $tokens): void
    {
        $this->tokens = $tokens;
    }

    public function assemble(): string
    {
        $resultCode = '';
        foreach ($this->tokens as $token) {
            if (Token::EMPTY === $token->getCodeType()) {
                continue;
            }
            $resultCode .= $token->getCode();
        }

        return $resultCode;
    }
}
