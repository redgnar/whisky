<?php

namespace Whisky\Test\Extension;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Whisky\Builder;
use Whisky\Builder\BasicBuilder;
use Whisky\Executor;
use Whisky\Executor\BasicExecutor;
use Whisky\Extension\BasicSecurity;
use Whisky\Extension\FunctionHandler;
use Whisky\Extension\VariableHandler;
use Whisky\Functions\FunctionRepository;
use Whisky\ParseError;
use Whisky\Parser\ParseResult;
use Whisky\Parser\PhpParser;
use Whisky\Scope;
use Whisky\Scope\BasicScope;

/**
 * Class BasicSecurityTest - it tests functionality of the BasicSecurity class.
 */
class BasicSecurityTest extends TestCase
{
    protected Builder $builder;
    protected Executor $executor;

    public function setUp(): void
    {
        parent::setUp();
        $functionRepository = new FunctionRepository();
        $this->builder = new BasicBuilder(
            new PhpParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7)),
            new VariableHandler(),
            new FunctionHandler($functionRepository)
        );
        $this->builder->addExtension(new BasicSecurity());
        $this->executor = new BasicExecutor($functionRepository);
    }

    public function testBuildMethod(): void
    {
        $basicSec = new BasicSecurity();
        $parseResultMock = $this->createMock(ParseResult::class);
        $parseResultMock->method('getFunctionCalls')->willReturn([]);

        // Test code without banned words and functions
        $validCode = 'myvariable = "no_banned_words_here";';
        $processedCode = $basicSec->build($validCode, $parseResultMock);
        $this->assertEquals($validCode, $processedCode, 'Ensuring that valid code is not changed.');

        // Test code with banned words
        $withBannedWords = 'die("this should not pass");';
        $this->expectException(ParseError::class);
        $basicSec->parse($withBannedWords);
    }

    public function testOkUsage(): void
    {
        $script = $this->builder->build('$a = 1;');
        self::assertEquals('$a = 1;', $script->getCode());
    }

    public function testNotAllowedThisUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$this->c = $a;');
    }

    public function testNotAllowedClassUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('class A {private $a = 1;}');
    }

    public function testClassInStringUsage(): void
    {
        $script = $this->builder->build('$a = "class";');
        self::assertEquals('$a = "class";', $script->getCode());
    }

    public function testNotAllowedDieUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$a = 1;die();');
    }

    public function testNotAllowedExitUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('$a = 1;exit;');
    }

    public function testNotAllowedFunctionUsage(): void
    {
        $this->expectException(ParseError::class);
        $this->builder->build('file_get_contents("path");');
    }
}
