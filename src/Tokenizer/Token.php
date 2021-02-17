<?php

declare(strict_types=1);

namespace Whisky\Tokenizer;

class Token
{
    private string $code;
    private int $codeType;

    public const WORD = 1;
    public const SPECIAL_WORD = 2;
    public const STRING = 3;
    public const EMPTY = 4;
    public const OTHER = 5;

    public function __construct(string $code, int $codeType)
    {
        $this->code = $code;
        $this->setCodeType($codeType);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCodeType(): int
    {
        return $this->codeType;
    }

    public function setCodeType(int $codeType): void
    {
        if ($codeType < self::WORD || $codeType > self::OTHER) {
            throw new \InvalidArgumentException('Wrong code type provided');
        }
        $this->codeType = $codeType;
    }
}
