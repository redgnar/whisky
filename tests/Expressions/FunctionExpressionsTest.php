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

class FunctionExpressionsTest extends TestCase
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

    public function testFunctionCallExpression(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('$b = substr("test string", 0, 5);');
        $this->executor->execute($script, $variables);
        self::assertEquals('test ', $variables->get('b'));
    }

    public function testFunctionCallFromProviderExpression(): void
    {
        $variables = new BasicScope();
        $this->functionProvider->addFunction('testIt', function (string $text) {return $text; });
        $script = $this->builder->build('$b = testIt("test string");');
        $this->executor->execute($script, $variables);
        self::assertEquals('test string', $variables->get('b'));
    }

    public function testNotExistingFunctionUsage(): void
    {
        $this->expectException(RunError::class);
        $variables = new BasicScope();
        $script = $this->builder->build('notExisting("path");');
        $this->executor->execute($script, $variables);
    }
}
