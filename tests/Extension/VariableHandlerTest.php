<?php

namespace Whisky\Test\Extension;

use PHPUnit\Framework\TestCase;
use Whisky\Extension\VariableHandler;
use Whisky\Parser\ParseResult;
use Whisky\Scope as WhiskyScope;

class VariableHandlerTest extends TestCase
{
    private VariableHandler $scope;

    protected function setUp(): void
    {
        $this->scope = new VariableHandler();
    }

    public function testBuild(): void
    {
        $parseResult = new ParseResult('parsedCode', ['inputVar'], ['outputVar'], ['functionCalls'], true);
        $expected = '$inputVar=$variables->get(\'inputVar\');if($variables->has(\'outputVar\'))$outputVar=$variables->get(\'outputVar\');'."\n".
            'parsedCode'."\n".
            '$variables->set(\'outputVar\', $outputVar);';

        // Act
        $actual = $this->scope->build('parsedCode', $parseResult);

        // Assert
        $this->assertSame($expected, $actual);
    }
}
