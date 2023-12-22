<?php

namespace Whisky\Test\Expressions;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\Extension\BasicSecurity;
use Whisky\Extension\FunctionProvider;
use Whisky\Extension\VariableHandler;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;
use Whisky\RunError;
use Whisky\Scope\BasicScope;

class ReturnExpressionsTest extends TestCase
{
    protected Builder $builder;
    protected Executor $executor;
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
        $this->executor = new BasicExecutor();
    }

    public function testReturnExpression(): void
    {
        $variables = new BasicScope();
        $variables->set('test', 'test string');
        $script = $this->builder->build('return ($test2 = $test);');
        $result = $this->executor->execute($script, $variables);
        self::assertEquals($variables->get('test'), $result);
        self::assertEquals($variables->get('test'), $variables->get('test2'));
    }

    public function testReturnNullExpression(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('return null;');
        $result = $this->executor->execute($script, $variables);
        self::assertEquals(null, $result);
    }

    public function testReturnFunctionUsage(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('return substr("Test Case", 0 , 4);');
        $this->executor->execute($script, $variables);
        $result = $this->executor->execute($script, $variables);
        self::assertEquals('Test', $result);
    }
}
