<?php

namespace Whisky\Test\Extension;

use PHPUnit\Framework\TestCase;
use Whisky\Extension\BasicSecurity;
use Whisky\ParseError;
use Whisky\Parser\ParseResult;
use Whisky\Scope;

/**
 * Class BasicSecurityTest - it tests functionality of the BasicSecurity class.
 */
class BasicSecurityTest extends TestCase
{
    public function testBuildMethod(): void
    {
        $basicSec = new BasicSecurity();
        $scopeMock = $this->createMock(Scope::class);
        $parseResultMock = $this->createMock(ParseResult::class);
        $parseResultMock->method('getFunctionCalls')->willReturn([]);

        // Test code without banned words and functions
        $validCode = 'myvariable = "no_banned_words_here";';
        $processedCode = $basicSec->build($validCode, $parseResultMock, $scopeMock);
        $this->assertEquals($validCode, $processedCode, 'Ensuring that valid code is not changed.');

        // Test code with banned words
        $withBannedWords = 'die("this should not pass");';
        $this->expectException(ParseError::class);
        $basicSec->build($withBannedWords, $parseResultMock, $scopeMock);
    }
}
