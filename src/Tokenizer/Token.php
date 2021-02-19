<?php

declare(strict_types=1);

namespace Whisky\Tokenizer;

class Token
{
    private string $code;
    private int $codeType;
    private int $index;

    public const WORD = 1;
    public const SPECIAL_WORD = 2;
    public const STRING = 3;
    public const SPACE = 4;
    public const OTHER = 5;

    public function __construct(string $code, int $codeType, int $index)
    {
        $this->code = $code;
        $this->setCodeType($codeType);
        $this->index = $index;
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

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }
}
