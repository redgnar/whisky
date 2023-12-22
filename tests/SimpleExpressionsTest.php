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
use Whisky\Extension\VariableHandler;
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

    public function testFunctionCallPassArgExpression(): void
    {
        $variables = new BasicScope();
        $variables->set('test', 'test string');
        $script = $this->builder->build('$b = substr($test, 0, 5);');
        $this->executor->execute($script, $variables);
        self::assertEquals('test ', $variables->get('b'));
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

    public function testForeachExpression(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('$c = []; foreach (["a", "b", "c"] as $a) {$c[] = $a;}');
        $this->executor->execute($script, $variables);
        self::assertEquals(['a', 'b', 'c'], $variables->get('c'));
    }

    public function testFunctionUsage(): void
    {
        $variables = new BasicScope();
        $script = $this->builder->build('$c = substr("Test Case", 0 , 4);');
        $this->executor->execute($script, $variables);
        self::assertEquals('Test', $variables->get('c'));
    }

    public function testComplexCodeExpression(): void
    {
        $variables = new BasicScope();
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
            );
        $this->executor->execute($script, $variables);
        self::assertEquals(['a'], $variables->get('result'));
    }

    public function testExecuteMissingVariable(): void
    {
        $this->expectException(RunError::class);
        $variables = new BasicScope();
        $script = $this->builder->build('foreach ($input as $a) {}');
        $this->executor->execute($script, $variables);
    }

    public function testNotExistingFunctionUsage(): void
    {
        $this->expectException(RunError::class);
        $variables = new BasicScope();
        $script = $this->builder->build('notExisting("path");');
        $this->executor->execute($script, $variables);
    }

    public function testParseError(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$c = 1}');
    }
}
