<?php

namespace Whisky\Test\Extension;

use PHPUnit\Framework\TestCase;
use Whisky\Extension\Scope;
use Whisky\Parser\ParseResult;
use Whisky\Scope as WhiskyScope;

class ScopeTest extends TestCase
{
    private Scope $scope;

    protected function setUp(): void
    {
        $this->scope = new Scope();
    }

    public function testBuild(): void
    {
        $parseResult = new ParseResult('parsedCode', ['inputVar'], ['outputVar'], ['functionCalls']);
        $whiskyScope = $this->createMock(\Whisky\Scope::class);
        // Prepare
        $whiskyScope->method('get')->willReturn('value');
        $expected = '$inputVar=$scope->get(\'inputVar\');'."\n".
            'parsedCode'."\n".
            '$scope->set(\'outputVar\', $outputVar);';

        // Act
        $actual = $this->scope->build('parsedCode', $parseResult, $whiskyScope);

        // Assert
        $this->assertSame($expected, $actual);
    }
}
