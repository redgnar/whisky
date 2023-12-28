<?php

namespace Whisky\Test\Builder;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Extension\BasicSecurity;
use Whisky\Extension\FunctionProvider;
use Whisky\Extension\VariableHandler;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;
use Whisky\Script;

class ParseTest extends TestCase
{
    protected Builder $builder;
    protected FunctionProvider $functionProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->functionProvider = new FunctionProvider();
        $this->builder = new BasicBuilder(
            new PhpParser((new ParserFactory())->create(ParserFactory::ONLY_PHP7))
        );
        $this->builder->addExtension(new BasicSecurity());
        $this->builder->addExtension(new VariableHandler());
        $this->builder->addExtension($this->functionProvider);
    }

    public function testNoInputAndOutput(): void
    {
        $script = $this->builder->build('return "Hello World";');
        self::assertInstanceOf(Script::class, $script);
        self::assertEmpty($script->getParseResult()->getInputVariables());
        self::assertEmpty($script->getParseResult()->getOutputVariables());
    }

    public function testParseError(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = 1}');
    }
}
