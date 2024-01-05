<?php

namespace Whisky\Test\Builder;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Extension\BasicSecurity;
use Whisky\Extension\FunctionHandler;
use Whisky\Extension\VariableHandler;
use Whisky\Function\FunctionRepository;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;
use Whisky\Script;

class ParseTest extends TestCase
{
    protected Builder $builder;
    protected FunctionHandler $functionHandler;

    protected FunctionRepository $functionRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->functionRepository = new FunctionRepository();
        $this->functionHandler = new FunctionHandler($this->functionRepository);
        $this->builder = new BasicBuilder(
            new PhpParser((new ParserFactory())->create(ParserFactory::ONLY_PHP7))
        );
        $this->builder->addExtension(new BasicSecurity());
        $this->builder->addExtension(new VariableHandler());
        $this->builder->addExtension($this->functionHandler);
    }

    public function testNoInputAndOutput(): void
    {
        $script = $this->builder->build('return "Hello World";');
        self::assertInstanceOf(Script::class, $script);
        self::assertEmpty($script->getParseResult()->getInputVariables());
        self::assertEmpty($script->getParseResult()->getOutputVariables());
        self::assertTrue($script->getParseResult()->hasReturnValue());
    }

    public function testOnlyInput(): void
    {
        $script = $this->builder->build('return $result;');
        self::assertInstanceOf(Script::class, $script);
        self::assertNotEmpty($script->getParseResult()->getInputVariables());
        self::assertContains('result', $script->getParseResult()->getInputVariables());
        self::assertEmpty($script->getParseResult()->getOutputVariables());
        self::assertTrue($script->getParseResult()->hasReturnValue());
    }

    public function testOnlyOutput(): void
    {
        $script = $this->builder->build('$result = "foo";');
        self::assertInstanceOf(Script::class, $script);
        self::assertEmpty($script->getParseResult()->getInputVariables());
        self::assertNotEmpty($script->getParseResult()->getOutputVariables());
        self::assertContains('result', $script->getParseResult()->getOutputVariables());
        self::assertFalse($script->getParseResult()->hasReturnValue());
    }

    public function testInputAndOutput(): void
    {
        $script = $this->builder->build('$result = implode(",", $collection); return $result;');
        self::assertInstanceOf(Script::class, $script);
        self::assertNotEmpty($script->getParseResult()->getInputVariables());
        self::assertContains('collection', $script->getParseResult()->getInputVariables());
        self::assertNotEmpty($script->getParseResult()->getOutputVariables());
        self::assertContains('result', $script->getParseResult()->getOutputVariables());
        self::assertTrue($script->getParseResult()->hasReturnValue());
    }

    public function testParseError(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = 1}');
    }
}
