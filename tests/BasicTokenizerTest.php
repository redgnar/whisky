<?php

namespace Whisky\Test;

use PHPUnit\Framework\TestCase;
use Whisky\Tokenizer;
use Whisky\Tokenizer\BasicTokenizer;
use Whisky\Tokenizer\Token;

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
        $stringTokens = $tokenizedCode->getTokensByCodeType(Token::STRING);
        self::assertCount(1, $stringTokens);
        self::assertEquals('"test string"', $stringTokens[0]->getCode());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assembleWithoutSpace());
    }

    public function testTokenizeStringWithEscapedQuotaAssignToVariableCode(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$a="test \"string\"";');
        self::assertCount(5, $tokenizedCode->getTokens());
        $stringTokens = $tokenizedCode->getTokensByCodeType(Token::STRING);
        self::assertCount(1, $stringTokens);
        self::assertEquals('"test \"string\""', $stringTokens[0]->getCode());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
    }

    public function testFunctionCallExpression(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$b = substr("test string", 0, 5);');
        self::assertCount(16, $tokenizedCode->getTokens());
        $wordTokens = $tokenizedCode->getTokensByCodeType(Token::WORD);
        self::assertCount(4, $wordTokens);
        self::assertEquals('b', $wordTokens[0]->getCode());
        self::assertEquals('substr', $wordTokens[1]->getCode());
        self::assertEquals('0', $wordTokens[2]->getCode());
        self::assertEquals('5', $wordTokens[3]->getCode());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
        self::assertNotEquals($tokenizedCode->getCode(), $tokenizedCode->assembleWithoutSpace());
    }

    public function testFunctionCallPassArgExpression(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$b = substr($test, 0, 5);');
        self::assertCount(17, $tokenizedCode->getTokens());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
    }

    public function testForeachExpression(): void
    {
        $tokenizedCode = $this->tokenizer->tokenize('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}');
        self::assertCount(40, $tokenizedCode->getTokens());
        self::assertEquals($tokenizedCode->getCode(), $tokenizedCode->assemble());
    }
}
