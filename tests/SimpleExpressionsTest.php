<?php

namespace Whisky\Test;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\Extension\BasicSecurity;
use Whisky\Extension\FunctionProvider;
use Whisky\Extension\Scope;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;
use Whisky\RunError;
use Whisky\Scope\BasicScope;

class SimpleExpressionsTest extends TestCase
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
        $this->builder->addExtension(new Scope());
        $this->builder->addExtension($this->functionProvider);
        $this->executor = new BasicExecutor();
    }

    public function testAssignExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$a="test string";', new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals('test string', $scope->get('a'));
    }

    public function testFunctionCallExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$b = substr("test string", 0, 5);', new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals('test ', $scope->get('b'));
    }

    public function testFunctionCallFromProviderExpression(): void
    {
        $scope = new BasicScope();
        $this->functionProvider->addFunction('testIt', function (string $text) {return $text; });
        $script = $this->builder->build('$b = testIt("test string");', new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals('test string', $scope->get('b'));
    }

    public function testFunctionCallPassArgExpression(): void
    {
        $scope = new BasicScope();
        $scope->set('test', 'test string');
        $script = $this->builder->build('$b = substr($test, 0, 5);', new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals('test ', $scope->get('b'));
    }

    public function testReturnExpression(): void
    {
        $scope = new BasicScope();
        $scope->set('test', 'test string');
        $script = $this->builder->build('return $test;', new BasicScope());
        $result = $this->executor->execute($script, $scope);
        self::assertEquals('test string', $result);
    }

    public function testForeachExpression(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}', new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals(['a', 'b', 'c'], $scope->get('c'));
    }

    public function testFunctionUsage(): void
    {
        $scope = new BasicScope();
        $script = $this->builder->build('$c = substr("Test Case", 0 , 4);', new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals('Test', $scope->get('c'));
    }

    public function testComplexCodeExpression(): void
    {
        $scope = new BasicScope();
        $this->functionProvider->addFunction('testIt', function (string $text) {return $text; });
        $script = $this->builder->build(
            <<<'EOD'
    $collection = ["a", "b"];
    $result = [];
    foreach ($collection as $item) {
        if ("b" === $item) {
            continue;
        }
        $result[] = testIt($item);
    }
EOD
            , new BasicScope());
        $this->executor->execute($script, $scope);
        self::assertEquals(['a'], $scope->get('result'));
    }

    public function testExecuteMissingVariable(): void
    {
        $this->expectException(RunError::class);
        $scope = new BasicScope();
        $script = $this->builder->build('foreach ($input as $a) {}', new BasicScope());
        $this->executor->execute($script, $scope);
    }

    public function testNotExistingFunctionUsage(): void
    {
        $this->expectException(RunError::class);
        $scope = new BasicScope();
        $script = $this->builder->build('notExisting("path");', new BasicScope());
        $this->executor->execute($script, $scope);
    }

    public function testParseError(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = 1}', new BasicScope());
    }
}
