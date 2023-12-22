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

class AssigmentExpressionsTest extends TestCase
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

    public function testAssignExpression(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('$a="test string";');
        $this->executor->execute($script, $variables);
        self::assertEquals('test string', $variables->get('a'));
    }

    public function testAssignFunctionCallPassArgExpression(): void
    {
        $variables = new BasicScope();
        $variables->set('test', 'test string');
        $script = $this->builder->build('$b = substr($test, 0, 5);');
        $this->executor->execute($script, $variables);
        self::assertEquals('test ', $variables->get('b'));
    }

    public function testExecuteMissingVariable1(): void
    {
        $this->expectException(RunError::class);
        $variables = new BasicScope();
        $script = $this->builder->build('$b = substr($test, 0, 5);');
        $this->executor->execute($script, $variables);
    }

    public function testExecuteMissingVariable2(): void
    {
        $this->expectException(RunError::class);
        $variables = new BasicScope();
        $script = $this->builder->build('foreach ($input as $a) {}');
        $this->executor->execute($script, $variables);
    }

    public function testForeachExpression(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}');
        $this->executor->execute($script, $variables);
        self::assertEquals(['a', 'b', 'c'], $variables->get('c'));
    }

    public function testParseError(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = 1}');
    }
}
