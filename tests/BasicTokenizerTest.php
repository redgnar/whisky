<?php

namespace Whisky\Test;

use PHPUnit\Framework\TestCase;
use Whisky\Tokenizer;
use Whisky\Tokenizer\BasicTokenizer;

class BasicTokenizerTest extends TestCase
{

    private Tokenizer $tokenizer;

    public function setUp(): void
    {
        parent::setUp();
        $this->tokenizer = new BasicTokenizer();
    }

    public function testTokenizeStringAssignToVariableCode(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$a="test string";');
        self::assertCount(5, $tokenizedCode->getTokens());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
    }

    public function testTokenizeStringWithEscapedQuotaAssignToVariableCode(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$a="test \"string\"";');
        self::assertCount(5, $tokenizedCode->getTokens());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
    }

    public function testFunctionCallExpression(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$b = substr("test string", 0, 5);');
        self::assertCount(12, $tokenizedCode->getTokens());
        self::assertEquals('$b=substr("test string",0,5);', $tokenizedCode->assemble());
    }

    public function testFunctionCallPassArgExpression(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$b = substr($test, 0, 5);');
        self::assertCount(13, $tokenizedCode->getTokens());
    }

    public function testForeachExpression(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}');
        self::assertCount(29, $tokenizedCode->getTokens());
    }
}
