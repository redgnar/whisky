<?php

namespace Whisky\Test;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\Parser\PhpParser;
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

    public function testForeachExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}');
        $this->executor->execute($script, $scope);
        self::assertEquals(['a', 'b', 'c'], $scope->get('c'));
    }
}
