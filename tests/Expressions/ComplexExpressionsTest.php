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
use Whisky\Parser\PhpParser;
use Whisky\Scope\BasicScope;

class ComplexExpressionsTest extends TestCase
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

    public function testComplexCodeExpression(): void
    {
        $variables = new BasicScope(['collection' => ['a', 'b']]);
        $this->functionProvider->addFunction('testIt', function (string $text) {return $text; });
        $script = $this->builder->build(
            <<<'EOD'
    $result = [];
    foreach ($collection as $item) {
        if ("b" === $item) {
            continue;
        }
        $result[] = testIt($item."aaa4bbb");
    }
EOD
        );
        $this->executor->execute($script, $variables);
        self::assertEquals(['aaaa4bbb'], $variables->get('result'));
    }

    public function testComplexCodeExpression2(): void
    {
        $variables = new BasicScope(['a' => new \stdClass()]);
        $script = $this->builder->build(
            <<<'EOD'
            $a->value = "test1";
            $a->value2 = "test2";
            $a->value3 = "test3";
EOD
        );
        $this->executor->execute($script, $variables);
        $a = $variables->get('a');
        self::assertIsObject($a);
        self::assertEquals('test3', $a->value3 ?? '');
    }
}
