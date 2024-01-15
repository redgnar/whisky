<?php

namespace Whisky\Test\Expressions;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\Extension\BasicSecurity;
use Whisky\Extension\FunctionHandler;
use Whisky\Extension\VariableHandler;
use Whisky\Function\FunctionRepository;
use Whisky\ParseError;
use Whisky\Parser\PhpParser;
use Whisky\RunError;
use Whisky\Scope\BasicScope;

class FunctionExpressionsTest extends TestCase
{
    protected Builder $builder;
    protected Executor $executor;
    protected FunctionHandler $functionHandler;
    protected FunctionRepository $functionRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->functionRepository = new FunctionRepository();
        $this->functionHandler = new FunctionHandler($this->functionRepository);
        $this->builder = new BasicBuilder(
            new PhpParser((new ParserFactory())->create(ParserFactory::ONLY_PHP7)),
            new VariableHandler(),
            $this->functionHandler
        );
        $this->builder->addExtension(new BasicSecurity());
        $this->executor = new BasicExecutor($this->functionRepository);
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
        $this->functionRepository->set('testIt', function (string $text) {return $text; });
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
