<?php

namespace Whisky\Test\Extension;

use PHPUnit\Framework\TestCase;
use Whisky\Extension\VariableHandler;
use Whisky\Parser\ParseResult;
use Whisky\Scope as WhiskyScope;

class VariableHandlerTest extends TestCase
{
    private VariableHandler $variableHandlercope;

    protected function setUp(): void
    {
        $this->variableHandlercope = new VariableHandler();
    }

    public function testBuild(): void
    {
        $parseResult = new ParseResult('parsedCode', ['inputVar'], ['outputVar'], ['functionCalls'], true);

        // Act
        $actual = $this->variableHandlercope->build('parsedCode', $parseResult);

        // Assert
        $this->assertSame('$inputVar=$variables->get(\'inputVar\');if($variables->has(\'outputVar\'))$outputVar=$variables->get(\'outputVar\');'."\n".
            'parsedCode'."\n".
            'if(isset($outputVar))$variables->set(\'outputVar\', $outputVar);', $actual);
    }
}
