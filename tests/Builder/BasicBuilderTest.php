<?php

namespace Whisky\Test\Builder;

use PHPUnit\Framework\TestCase;
use Whisky\Builder\BasicBuilder;
use Whisky\Extension;
use Whisky\Parser;
use Whisky\Parser\ParseResult;
use Whisky\Scope;
use Whisky\Script\BasicScript;

class BasicBuilderTest extends TestCase
{
    /**
     * Test build method for BasicBuilder class.
     * This test case assumes a script is being built successfully.
     */
    public function testBuild(): void
    {
        $code = 'echo "Hello, World!";';
        $parser = $this->createMock(Parser::class);
        $builder = new BasicBuilder($parser);
        $environment = $this->createMock(Scope::class);
        $parseResult = new ParseResult($code, [], [], []);
        $extension = $this->createMock(Extension::class);
        $parser->method('parse')->willReturn($parseResult);
        $extension->method('build')->willReturn($code);

        $builder->addExtension($extension);
        $result = $builder->build($code, $environment);

        $this->assertInstanceOf(BasicScript::class, $result);
    }

}
