<?php

namespace Whisky\Test;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\Extension\BasicSecurity;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;

class BasicSecurityTest extends TestCase
{
    protected Builder $builder;
    protected Executor $executor;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new BasicBuilder(
            new PhpParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7))
        );
        $this->builder->addExtension(new BasicSecurity());
        $this->executor = new BasicExecutor();
    }

    public function testOkUsage(): void
    {
        $script = $this->builder->build('$a = 1;');
        self::assertEquals('$a = 1;', $script->getCode());
    }

    public function testNotAllowedThisUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$this->c = $a;');
    }

    public function testNotAllowedWhileUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = []; $i = 0; while (true) {$c[] = $i++;}');
    }

    public function testNotAllowedClassUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('class A {private $a = 1;}');
    }

    public function testOkInStringUsage(): void
    {
        $script = $this->builder->build('$a = "class";');
        self::assertEquals('$a = "class";', $script->getCode());
    }
}
