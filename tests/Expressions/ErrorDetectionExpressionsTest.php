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
use Whisky\RunError;
use Whisky\Scope\BasicScope;

class ErrorDetectionExpressionsTest extends TestCase
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
        $variables = new BasicScope(['collection' => 's']);
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
        try {
            $this->executor->execute($script, $variables);
        } catch (RunError $error) {
            self::assertStringContainsString('foreach() argument', $error->getMessage());
        }
    }
}
