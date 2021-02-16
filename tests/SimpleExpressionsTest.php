<?php

namespace Whisky\Test;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;
use Whisky\RunError;
use Whisky\Scope\BasicScope;

class SimpleExpressionsTest extends TestCase
{
    protected Builder $builder;
    protected Executor $executor;

    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new BasicBuilder(
            new PhpParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7))
        );

        $this->executor = new BasicExecutor();
    }

    public function testAssignExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$a="test string";');
        $this->executor->execute($script, $scope);
        self::assertEquals('test string', $scope->get('a'));
    }

    public function testFunctionCallExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$b = substr("test string", 0, 5);');
        $this->executor->execute($script, $scope);
        self::assertEquals('test ', $scope->get('b'));
    }

    public function testFunctionCallPassArgExpression(): void
    {
        $scope = new BasicScope();
        $scope->set('test', 'test string');
        $script = $this->builder->build('$b = substr($test, 0, 5);');
        $this->executor->execute($script, $scope);
        self::assertEquals('test ', $scope->get('b'));
    }

    public function testForeachExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}');
        $this->executor->execute($script, $scope);
        self::assertEquals(['a', 'b', 'c'], $scope->get('c'));
    }

    public function testFunctionUsage(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$c = substr("Test Case", 0 , 4);');
        $this->executor->execute($script, $scope);
        self::assertEquals('Test', $scope->get('c'));

    }

    public function testExecuteMissingVariable(): void
    {
        $this->expectException(RunError::class);
        $scope = new BasicScope();
        $script = $this->builder->build('foreach ($input as $a) {}');
        $this->executor->execute($script, $scope);
    }

    public function testParseError(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = 1}');
    }
}
