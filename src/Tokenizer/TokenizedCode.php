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
     * @return Token[]
     */
    public function getTokensByCodeType(int $codeType): array
    {
        $result = [];
        foreach ($this->tokens as $token) {
            if ($token->getCodeType() === $codeType) {
                $result[] = $token;
            }
        }

        return $result;
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
            $resultCode .= $token->getCode();
        }

        return $resultCode;
    }

    public function assembleWithoutSpace(): string
    {
        $resultCode = '';
        foreach ($this->tokens as $token) {
            if (Token::SPACE === $token->getCodeType()) {
                continue;
            }
            $resultCode .= $token->getCode();
        }

        return $resultCode;
    }
}
