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
use Whisky\Parser\PhpParser;
use Whisky\Scope\BasicScope;

class ComplexExpressionsTest extends TestCase
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

    public function testComplexCodeExpression(): void
    {
        $variables = new BasicScope(['collection' => ['a', 'b']]);
        $this->functionRepository->set('testIt', function (string $text) {return $text; });
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

    public function testComplexCodeExpression3(): void
    {
        $variables = new BasicScope(['fruits' => ["d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple"]]);
        $script = $this->builder->build(
            <<<'EOD'
            array_walk($fruits, function (&$item1, $key, $prefix){
                $item1 = "$prefix: $item1";
            }, 'fruit');
EOD
        );
        $this->executor->execute($script, $variables);
        $fruits = $variables->get('fruits');
        self::assertIsArray($fruits);
        self::assertEquals('fruit: orange', $fruits['a']);
    }

    public function testComplexCodeExpression4(): void
    {
        $variables = new BasicScope(['test1' => 1, 'test2' => 2]);
        $script = $this->builder->build(
            <<<'EOD'
            if ($test1 === $test2) {
                $output = 2;
            } else {
                $output = 1;
            }
EOD
        );
        $this->executor->execute($script, $variables);
        $output = $variables->get('output');
        self::assertIsInt($output);
        self::assertEquals(1, $output);
    }

    public function testComplexCodeExpression5(): void
    {
        $variables = new BasicScope(['test1' => 1, 'test2' => 2]);
        $script = $this->builder->build(
            <<<'EOD'
            $output = [$test1, $test2];
EOD
        );
        $this->executor->execute($script, $variables);
        $output = $variables->get('output');
        self::assertIsArray($output);
        self::assertEquals([1, 2], $output);
    }

    public function testComplexCodeExpression6(): void
    {
        $variables = new BasicScope(['originally_accepting' => 2, 'checker' => 4]);
        $script = $this->builder->build(
            <<<'EOD'
            $displayAs = 'Display Text';
            
            if ($checker == $originally_accepting) {
                $firstly_assigned = ['usr_id' => 1];
                if($firstly_assigned['usr_id'] == $checker) {
                    $result = [
                        'id_run' => $checker,
                        'id_should_run' => $checker,
                        'checker' => $displayAs,
                    ];
                } else {
                    $result = [
                        'id_run' => $checker,
                        'id_should_run' => $firstly_assigned['usr_id'],
                        'checker' => $displayAs.' :: FOO',
                    ];
                }
            } else {
                $result = [
                    'id_run' => $originally_accepting,
                    'id_should_run' => $checker,
                    'checker' => 'FOO :: '.$displayAs,
                ];
            }
EOD
        );

        $this->executor->execute($script, $variables);
        $result = $variables->get('result');
        self::assertIsArray($result);
        self::assertEquals(2, $result['id_run']);
    }
}
